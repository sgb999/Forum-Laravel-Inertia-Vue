import { User } from "./User";
import { Post } from "./Post";

export interface Comment {
    id: number;
    comment: string;
    user_id?: number;
    post_id?: number;
    createdAt: string;
    updatedAt: string;
    user?: User;
    post?: Post;
}
