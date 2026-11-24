#!/usr/bin/env ruby
# Renders config/deploy.yml (+ config/deploy.staging.yml and
# config/deploy.production.yml) with REVERB_APP_KEY/REVERB_HOST missing, blank
# and set, proving require_env! rejects missing and whitespace-only values in
# both destinations (used by .github/workflows/tests.yml).

require "erb"
require "yaml"

CONFIG_DIR = File.expand_path("../config", __dir__)
CONFIG_FILES = %w[deploy.yml deploy.staging.yml deploy.production.yml].map do |name|
  File.join(CONFIG_DIR, name)
end

def render_with(env)
  previous = env.keys.to_h { |key| [key, ENV[key]] }
  env.each { |key, value| value.nil? ? ENV.delete(key) : ENV[key] = value }
  CONFIG_FILES.each do |file|
    rendered = ERB.new(File.read(file), trim_mode: "-").result(binding)
    YAML.safe_load(rendered)
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

def expect_renders(label, env)
  render_with(env)
  puts "PASS: #{label}"
rescue KeyError => e
  warn "FAIL: #{label} raised #{e.message}"
  exit 1
end

valid = { "REVERB_APP_KEY" => "local-key", "REVERB_HOST" => "reverb.example.com" }

expect_raises("REVERB_APP_KEY missing fails the build", valid.merge("REVERB_APP_KEY" => nil))
expect_raises("REVERB_APP_KEY blank fails the build", valid.merge("REVERB_APP_KEY" => "   "))
expect_raises("REVERB_HOST missing fails the build", valid.merge("REVERB_HOST" => nil))
expect_raises("REVERB_HOST blank fails the build", valid.merge("REVERB_HOST" => ""))
expect_renders("REVERB_APP_KEY and REVERB_HOST set renders all deploy configs", valid)
