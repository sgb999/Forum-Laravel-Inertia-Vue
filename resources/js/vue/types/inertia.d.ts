// global.d.ts
import '@inertiajs/core'

declare module "@inertiajs/core" {
    export interface InertiaConfig {
        sharedPageProps: {
            auth: { login: boolean, user: { id: number; username: string } | null };
            appName: string;
            csrf: string;
        };
        flashDataType: {
            toast?: { type: "success" | "error"; message: string };
        };
        errorValueType: string[];
        layoutProps: {
            title: string;
            showSidebar: boolean;
        };
        namedLayoutProps: {
            app: { title: string; theme: "light" | "dark" };
            content: { padding: string; maxWidth: string };
        };
    }
}
