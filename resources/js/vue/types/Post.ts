import {User} from "./User";
import {Category} from "./Category";

export interface Post {
    id: number;
    title: string;
    content: string;
    user_id?: number;
    user?: User;
    category_id?: number;
    category?: Category;
    createdAt: string;
    updatedAt: string;
}
