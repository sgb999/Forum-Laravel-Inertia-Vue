import {MediaLibrary} from "./MediaLibrary";

export interface User {
    id: number;
    username: string;
    email: string | null;
    createdAt: string | null;
    updatedAt: string | null;
    profilePicture?: MediaLibrary;
    bannerPicture?: MediaLibrary;
}
