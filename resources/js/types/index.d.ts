import type { Config } from 'ziggy-js';

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    ziggy: Config & { location: string };
};

export interface AuthPageProps extends AppPageProps {
    auth: Auth;
}

export interface StorePageProps extends AuthPageProps {
    stores: Store[];
}

export interface Auth {
    user?: User
}

export interface User {
    id: number;
    student_id: string;
    name: string;
    course_id: number;
    seat_number: number;
    enabled: number;
    created_at: string;
    updated_at: string;
}

export interface Store {
    id: number;
    name: string;
    address: string;
    google_map: string;
    phone: string;
    image: string;
    is_open: boolean;
    created_at: string;
    updated_at: string;
}
