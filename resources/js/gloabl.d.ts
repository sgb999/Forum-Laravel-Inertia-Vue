import '@inertiajs/core'
import { route as routeFn } from 'ziggy-js';

declare global {
    const route: typeof routeFn;
}

declare module 'vue' {
    interface ComponentCustomProperties {
        route: typeof routeFn;
    }
}

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
