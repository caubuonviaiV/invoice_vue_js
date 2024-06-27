<script>
import { onMounted, ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

export default {
    setup() {
        let invoices = ref([]);
        let searchInvoice = ref([]);

        const router = useRouter();

        onMounted(async () => {
            await fetchInvoicesData();
        });

        const fetchInvoicesData = async () => {
            try {
                let response = await axios.get("/api/invoices");
                invoices.value = response.data;
            } catch (error) {
                console.error("Error fetching invoices:", error);
            }
        };

        const search = async () => {
            try {
                let response = await axios.get(
                    "/api/invoices/search?s=" + searchInvoice.value
                );
                invoices.value = response.data;
            } catch (error) {
                console.error("Error fetching invoices:", error);
            }
        };

        const newInvocie = async () => {
            let form = axios.get("/api/invoices/create");
            console.log("Form", form.data);
            router.push("invoices/new");
        };

        const onShow = (id) => {
            router.push("/invoices/show/" + id);
        };

        // Helper function to format date
        const formatDate = (dateString) => {
            const date = new Date(dateString);
            return date.toLocaleDateString();
        };

        return {
            invoices,
            fetchInvoicesData,
            searchInvoice,
            search,
            formatDate,
            newInvocie,
            onShow,
        };
    },
};
</script>

<template>
    <div class="container">
        <!--==================== INVOICE LIST ====================-->
        <div class="invoices">
            <div class="card__header">
                <div>
                    <h2 class="invoice__title">Invoices</h2>
                </div>
                <div>
                    <a class="btn btn-secondary" @click="newInvocie">
                        New Invoice
                    </a>
                </div>
            </div>

            <div class="table card__content">
                <div class="table--filter">
                    <span class="table--filter--collapseBtn">
                        <i class="fas fa-ellipsis-h"></i>
                    </span>
                    <div class="table--filter--listWrapper">
                        <ul class="table--filter--list">
                            <li>
                                <p
                                    class="table--filter--link table--filter--link--active"
                                >
                                    All
                                </p>
                            </li>
                            <li>
                                <p class="table--filter--link">Paid</p>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="table--search">
                    <div class="table--search--wrapper">
                        <select class="table--search--select" name="" id="">
                            <option value="">Filter</option>
                        </select>
                    </div>
                    <div class="relative">
                        <i class="table--search--input--icon fas fa-search"></i>
                        <input
                            class="table--search--input"
                            type="text"
                            placeholder="Search invoice"
                            v-model="searchInvoice"
                            @keyup="search()"
                        />
                    </div>
                </div>

                <div class="table--heading">
                    <p>ID</p>
                    <p>Date</p>
                    <p>Number</p>
                    <p>Customer</p>
                    <p>Due Date</p>
                    <p>Total</p>
                </div>

                <!-- item 1 -->
                <div v-if="invoices.length > 0">
                    <div
                        v-for="invoice in invoices"
                        :key="invoice.id"
                        class="table--items"
                    >
                        <a
                            href="#"
                            @click="onShow(invoice.id)"
                            class="table--items--transactionId"
                            >#{{ invoice.id }}</a
                        >
                        <p>{{ formatDate(invoice.date) }}</p>
                        <p>#{{ invoice.number }}</p>
                        <p v-if="invoice.customer">
                            {{ invoice.customer.first_name }}
                        </p>
                        <p v-else>No customer information</p>

                        <p>{{ formatDate(invoice.due_date) }}</p>
                        <p>${{ invoice.total }}</p>
                    </div>
                </div>
                <div v-else class="table--items">
                    <p>Invoice not found</p>
                </div>
            </div>
        </div>
    </div>
</template>
