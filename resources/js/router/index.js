import { createRouter, createWebHistory } from "vue-router";

import invoiceIndex from '../components/invoices/index.vue';
import invoiceNew from '../components/invoices/new.vue';
import invoiceShow from '../components/invoices/show.vue';
import invoiceEdit from '../components/invoices/edit.vue';
import noteFound from '../components/NotFound.vue';


const routes = [
    {
        path: '/',
        component: invoiceIndex
    },
    {
        path: '/invoices/new',
        component: invoiceNew
    },
    {
        path: '/invoices/show/:id',
        component: invoiceShow,
        props: true,
    },
    {
        path: '/invoices/edit/:id',
        component: invoiceEdit,
        props: true,
    },
    {
        path: '/:pathMatch(.*)*',
        component: noteFound,
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
