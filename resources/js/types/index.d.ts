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

export interface StoreShowPageProps extends AuthPageProps {
    store: Store;
    menuItems: MenuItem[];
}

export interface GroupOrderPageProps extends AuthPageProps {
    groupOrders: GroupOrder[];
}

export interface GroupOrderShowPageProps extends AuthPageProps {
    groupOrder: GroupOrder;
    orders: Order[];
    myOrder: Order | null;
}

export interface GroupOrdersOrderPageProps extends AuthPageProps {
    groupOrder: GroupOrder;
    myOrder: Order | null;
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
    facebook: string;
    instagram: string;
    business_hours: string;
    delivery_conditions: string;
    phone: string;
    image: string;
    is_closed: boolean;
    created_at: string;
    updated_at: string;
}

export interface MenuItem {
    id: number;
    store_id: number;
    name: string;
    price: number;
    is_available: boolean;
    created_at: string;
    updated_at: string;
}

export interface GroupOrder {
    id: number;
    store_id: number;
    user_id: number;
    status: 'open'|'closed'|'ordered';
    menu_snapshot: MenuItem[];
    store: Store;
    user: User;
    created_at: string;
    updated_at: string;
}

export interface Order {
    id: number;
    total_price: string;
    user: User;
    order_items: OrderItem[];
    created_at: string;
    updated_at: string;
}

export interface OrderItem {
    id: number;
    order_id: number;
    menu_item_id: number;
    name: string;
    price: number;
    quantity: number;
    comment: string;
}
