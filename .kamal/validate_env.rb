#!/usr/bin/env ruby
# Renders config/deploy.yml (+ config/deploy.staging.yml and
# config/deploy.production.yml) with REVERB_APP_KEY/REVERB_HOST missing, blank
# and set, proving require_env! rejects missing and whitespace-only values in
# both destinations, and asserts that staging and production use separate
# images, publish no fixed host ports on app roles, and run on non-superuser
# DB roles distinct from the Postgres accessory admin (used by
# .github/workflows/tests.yml).

require "erb"
require "yaml"

CONFIG_DIR = File.expand_path("../config", __dir__)
CONFIG_FILES = %w[deploy.yml deploy.staging.yml deploy.production.yml].map do |name|
  File.join(CONFIG_DIR, name)
end

def render_with(env)
  previous = env.keys.to_h { |key| [key, ENV[key]] }
  env.each { |key, value| value.nil? ? ENV.delete(key) : ENV[key] = value }
  CONFIG_FILES.to_h do |file|
    rendered = ERB.new(File.read(file), trim_mode: "-").result(binding)
    [File.basename(file), YAML.safe_load(rendered)]
  end
ensure
  previous.each { |key, value| value.nil? ? ENV.delete(key) : ENV[key] = value }
end

def expect_raises(label, env)
  render_with(env)
  warn "FAIL: #{label} rendered but should have raised KeyError"
  exit 1
rescue KeyError
  puts "PASS: #{label}"
end

def assert!(label, condition)
  unless condition
    warn "FAIL: #{label}"
    exit 1
  end
  puts "PASS: #{label}"
end

valid = { "REVERB_APP_KEY" => "local-key", "REVERB_HOST" => "reverb.example.com" }

expect_raises("REVERB_APP_KEY missing fails the build", valid.merge("REVERB_APP_KEY" => nil))
expect_raises("REVERB_APP_KEY blank fails the build", valid.merge("REVERB_APP_KEY" => "   "))
expect_raises("REVERB_HOST missing fails the build", valid.merge("REVERB_HOST" => nil))
expect_raises("REVERB_HOST blank fails the build", valid.merge("REVERB_HOST" => ""))

configs = render_with(valid)
puts "PASS: REVERB_APP_KEY and REVERB_HOST set renders all deploy configs"

base = configs.fetch("deploy.yml")
staging = configs.fetch("deploy.staging.yml")
production = configs.fetch("deploy.production.yml")

configs.each do |name, cfg|
  servers = cfg["servers"] || {}
  servers.each do |role, role_cfg|
    next unless role_cfg.is_a?(Hash)
    options = role_cfg["options"] || {}
    assert!("#{name} servers.#{role} has no fixed host port publish option", !options.key?("publish"))
  end
end

assert!(
  "staging and production declare distinct image names",
  !staging["image"].to_s.strip.empty? &&
    !production["image"].to_s.strip.empty? &&
    staging["image"] != production["image"] &&
    staging["image"] != base["image"] &&
    production["image"] != base["image"]
)

pg_admin = production.dig("accessories", "postgres", "env", "clear", "POSTGRES_USER")
prod_db_user = production.dig("env", "clear", "DB_USERNAME")
staging_db_user = staging.dig("env", "clear", "DB_USERNAME")

assert!(
  "postgres accessory superuser is distinct from production and staging app DB users",
  !pg_admin.to_s.strip.empty? &&
    pg_admin != prod_db_user &&
    pg_admin != staging_db_user &&
    prod_db_user != staging_db_user
)

prod_secrets = File.read(File.expand_path("secrets.production.example", __dir__))
assert!(
  "secrets.production.example uses a dedicated POSTGRES_PASSWORD distinct from PRODUCTION_DB_PASSWORD",
  !prod_secrets.match?(/^\s*POSTGRES_PASSWORD=\$\{?PRODUCTION_DB_PASSWORD\}?\s*$/)
)
assert!(
  "secrets.production.example does not fall back to RUSTFS_ACCESS_KEY or RUSTFS_SECRET_KEY for app AWS keys",
  !prod_secrets.include?("RUSTFS_ACCESS_KEY}") && !prod_secrets.include?("RUSTFS_SECRET_KEY}")
)

pg_secret_env = Array(production.dig("accessories", "postgres", "env", "secret"))
assert!(
  "postgres accessory injects PRODUCTION_DB_PASSWORD and STAGING_DB_PASSWORD via env.secret",
  pg_secret_env.include?("PRODUCTION_DB_PASSWORD") && pg_secret_env.include?("STAGING_DB_PASSWORD")
)

init_script = File.read(File.expand_path("../docker/postgres/init-staging.sh", __dir__))
assert!(
  "init-staging.sh reads role passwords inside psql via \\getenv without passing them on the psql command line",
  init_script.include?('\\getenv prod_password PRODUCTION_DB_PASSWORD') &&
    init_script.include?('\\getenv staging_password STAGING_DB_PASSWORD') &&
    !init_script.include?("--set=prod_password=") &&
    !init_script.include?("--set=staging_password=")
)

readme = File.read(File.expand_path("../README.md", __dir__))
assert!(
  "README bootstrap command does not pass PRODUCTION_DB_PASSWORD or STAGING_DB_PASSWORD on a command line",
  !readme.match?(/PRODUCTION_DB_PASSWORD\s*=/) && !readme.match?(/STAGING_DB_PASSWORD\s*=/)
)

compose_raw = File.read(File.expand_path("../docker-compose.yml", __dir__))
assert!(
  "docker-compose.yml binds postgres port to 127.0.0.1 and requires DB_PASSWORD without a predictable default",
  compose_raw.include?('"127.0.0.1:${FORWARD_DB_PORT:-5432}:5432"') &&
    compose_raw.include?("POSTGRES_PASSWORD: ${DB_PASSWORD:?") &&
    !compose_raw.include?("DB_PASSWORD:-password")
)

assert!(
  "init-staging.sh rejects equal or admin-colliding database names before running pg_isready or psql",
  init_script.include?('if [ "$PROD_DB" = "$ADMIN_DB" ] || [ "$STAGING_DB" = "$ADMIN_DB" ]; then') &&
    init_script.include?('if [ "$PROD_DB" = "$STAGING_DB" ]; then') &&
    init_script.index('if [ "$PROD_DB" = "$STAGING_DB" ]; then') < init_script.index('pg_isready')
)

dockerignore = File.read(File.expand_path("../.dockerignore", __dir__)).lines.map(&:strip)
assert!(
  ".dockerignore excludes auth.json and storage/*.key",
  dockerignore.include?("auth.json") && dockerignore.include?("storage/*.key")
)
