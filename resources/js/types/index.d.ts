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
    groupOrders: (GroupOrder & {
        store: Store;
        course: Course;
    })[];
    myComment: Comment;
    comments: Comment[];
}

export interface StoreEditPageProps extends AuthPageProps {
    store?: Store;
    menuItems?: MenuItem[];
}
export interface GroupOrderPageProps extends AuthPageProps {
    groupOrders: (GroupOrder & {
        store: Store;
        course: Course;
    })[];
}

export interface GroupOrderShowPageProps extends AuthPageProps {
    groupOrder: (GroupOrder & {
        store: Store;
        course: Course;
        menu_snapshot: MenuItem[];
    });
    orders: Order[];
    myOrder: Order | null;
}

export interface GroupOrdersOrderPageProps extends AuthPageProps {
    groupOrder: (GroupOrder & {
        store: Store;
        course: Course;
        menu_snapshot: MenuItem[];
    });
    myOrder: Order | null;
}

export interface OrderPageProps extends AuthPageProps {
    orders: Order[];
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
    course?: Course;
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
    ordered_group_orders_count: number;
    course_ordered_group_orders_count: number;
    rating_avg?: number | null;
    rating_count?: number | null;
    course_rating_avg?: number | null;
    course_rating_count?: number | null;
}

export interface MenuItem {
    id: number;
    store_id: number;
    name: string;
    price: number;
    is_available: boolean;
    created_at: string;
    updated_at: string;
    total_ordered_count?: number;
    course_ordered_count?: number;
}

export type GroupOrderStatus = 'open' | 'closed' | 'ordered';
export interface GroupOrder {
    id: number;
    store_id: number;
    user_id: number;
    is_public: boolean;
    status: GroupOrderStatus;
    user: User;
    created_at: string;
    updated_at: string;
}

export interface Order {
    id: number;
    total_price: number;
    user: User;
    order_items: OrderItem[];
    created_at: string;
    updated_at: string;
    group_order?: GroupOrder;
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

export interface Course {
    id: number;
    year: number;
    term: number;
    name: string;
    created_at: string;
    updated_at: string;
}

export interface Comment {
    id: number;
    store_id: number;
    rating: number;
    content: string;
    created_at: string;
    updated_at: string;
}
