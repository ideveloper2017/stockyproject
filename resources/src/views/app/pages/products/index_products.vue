<template>
    <div class="main-content">
        <breadcumb :page="$t('productsList')" :folder="$t('Products')"/>
        <div v-if="isLoading" class="loading_page spinner spinner-primary mr-3"></div>
        <div v-else>
            <!-- Summary cards: each total shown separately (ALL filtered products) -->
            <b-row class="mb-3">
                <b-col md="2" class="mb-2">
                    <b-card body-class="py-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Soni</div>
                            <h4 class="mb-0">{{ totalsAllCount }}</h4>
                        </div>
                        <div v-if="totalsLoading" class="spinner sm spinner-primary"></div>
                    </b-card>
                </b-col>
                <b-col md="2" class="mb-2">
                    <b-card body-class="py-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Miqdori</div>
                            <h4 class="mb-0">{{ totalsAllQtyFormatted }}</h4>
                        </div>
                        <div v-if="totalsLoading" class="spinner sm spinner-primary"></div>
                    </b-card>
                </b-col>
                <b-col md="2" class="mb-2">
                    <b-card body-class="py-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Jami narx</div>
                            <h4 class="mb-0">{{ totalsAllPriceFormatted }}</h4>
                        </div>
                        <div v-if="totalsLoading" class="spinner sm spinner-primary"></div>
                    </b-card>
                </b-col>
                <b-col md="2" class="mb-2">
                    <b-card body-class="py-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Jami krim narx</div>
                            <h4 class="mb-0">{{ totalsAllCostFormatted }}</h4>
                        </div>
                        <div v-if="totalsLoading" class="spinner sm spinner-primary"></div>
                    </b-card>
                </b-col>
                <b-col md="2" class="mb-2">
                    <b-card body-class="py-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Umumiy summa</div>
                            <h4 class="mb-0">{{ totalsAllProductSummaFormatted }}</h4>
                        </div>
                        <div v-if="totalsLoading" class="spinner sm spinner-primary"></div>
                    </b-card>
                </b-col>
            </b-row>

            <vue-good-table
                mode="remote"
                :columns="columns"
                :totalRows="totalRows"
                :rows="products"
                @on-page-change="onPageChange"
                @on-per-page-change="onPerPageChange"
                @on-sort-change="onSortChange"
                @on-search="onSearch"
                :select-options="{
          enabled: true ,
          clearSelectionText: '',
        }"
                @on-selected-rows-change="selectionChanged"
                :search-options="{
          enabled: true,
          placeholder: $t('Search_this_table'),
        }"
                :pagination-options="{
        enabled: true,
        mode: 'records',
        nextLabel: 'next',
        prevLabel: 'prev',
      }"
                styleClass="tableOne vgt-table"
            >
                <div slot="selected-row-actions">
                    <button class="btn btn-danger" @click="delete_by_selected()">{{$t('Del')}}</button>
                </div>
                <div slot="table-actions" class="mt-2 mb-3">
                    <b-button variant="outline-info m-1" size="sm" v-b-toggle.sidebar-right>
                        <i class="i-Filter-2"></i>
                        {{ $t("Filter") }}
                    </b-button>
                    <b-button @click="Product_PDF()" size="sm" variant="outline-success m-1">
                        <i class="i-File-Copy"></i> PDF
                    </b-button>
                    <vue-excel-xlsx
                        class="btn btn-sm btn-outline-danger ripple m-1"
                        :data="excelData"
                        :columns="excelColumns"
                        :file-name="'products'"
                        :file-type="'xlsx'"
                        :sheet-name="'products'"
                    >
                        <i class="i-File-Excel"></i> EXCEL
                    </vue-excel-xlsx>
                    <b-button
                        @click="Show_import_products()"
                        size="sm"
                        variant="info m-1"
                        v-if="currentUserPermissions && currentUserPermissions.includes('product_import')"
                    >
                        <i class="i-Download"></i>
                        {{ $t("import_products") }}
                    </b-button>
                    <router-link
                        class="btn-sm btn btn-primary btn-icon m-1"
                        v-if="currentUserPermissions && currentUserPermissions.includes('products_add')"
                        to="/app/products/store"
                    >
            <span class="ul-btn__icon">
              <i class="i-Add"></i>
            </span>
                        <span class="ul-btn__text ml-1">{{$t('Add')}}</span>
                    </router-link>
                </div>
      <template slot="table-row" slot-scope="props">
          <span v-if="props.column.field == 'actions'">
            <router-link
                v-if="currentUserPermissions && currentUserPermissions.includes('products_view')"
                v-b-tooltip.hover
                title="View"
                :to="{ name:'detail_product', params: { id: props.row.id} }"
            >
              <i class="i-Eye text-25 text-info"></i>
            </router-link>
            <router-link
                v-if="currentUserPermissions && currentUserPermissions.includes('products_edit')"
                v-b-tooltip.hover
                title="Edit"
                :to="{ name:'edit_product', params: { id: props.row.id } }"
            >
              <i class="i-Edit text-25 text-success"></i>
            </router-link>
            <a
                v-if="currentUserPermissions && currentUserPermissions.includes('products_delete')"
                @click="Remove_Product(props.row.id)"
                v-b-tooltip.hover
                title="Delete"
                class="cursor-pointer"
            >
              <i class="i-Close-Window text-25 text-danger"></i>
            </a>
          </span>
          <span v-else-if="props.column.field == 'image'">
            <b-img
                thumbnail
                height="50"
                width="50"
                fluid
                :src="'/images/products/' + props.row.image"
                alt="image"
            ></b-img>
          </span>
                </template>
            </vue-good-table>

            <!-- Multiple filter -->
            <b-sidebar id="sidebar-right" :title="$t('Filter')" bg-variant="white" right shadow>
                <div class="px-3 py-2">
                    <b-row>
                        <!-- Code Product  -->
                        <b-col md="12">
                            <b-form-group :label="$t('CodeProduct')">
                                <b-form-input
                                    label="Code Product"
                                    :placeholder="$t('SearchByCode')"
                                    v-model="Filter_code"
                                ></b-form-input>
                            </b-form-group>
                        </b-col>

                        <!-- Name  -->
                        <b-col md="12">
                            <b-form-group :label="$t('ProductName')">
                                <b-form-input
                                    label="Name Product"
                                    :placeholder="$t('SearchByName')"
                                    v-model="Filter_name"
                                ></b-form-input>
                            </b-form-group>
                        </b-col>

                        <!-- Category  -->
                        <b-col md="12">
                            <b-form-group :label="$t('Categorie')">
                                <v-select
                                    :reduce="label => label.value"
                                    :placeholder="$t('Choose_Category')"
                                    v-model="Filter_category"
                                    :options="categories.map(categories => ({label: categories.name, value: categories.id}))"
                                />
                            </b-form-group>
                        </b-col>

                        <!-- Brand  -->
                        <b-col md="12">
                            <b-form-group :label="$t('Brand')">
                                <v-select
                                    :reduce="label => label.value"
                                    :placeholder="$t('Choose_Brand')"
                                    v-model="Filter_brand"
                                    :options="brands.map(brands => ({label: brands.name, value: brands.id}))"
                                />
                            </b-form-group>
                        </b-col>

                        <b-col md="6" sm="12">
                            <b-button
                                @click="Get_Products(serverParams.page)"
                                variant="primary m-1"
                                size="sm"
                                block
                            >
                                <i class="i-Filter-2"></i>
                                {{ $t("Filter") }}
                            </b-button>
                        </b-col>

                        <b-col md="6" sm="12">
                            <b-button @click="Reset_Filter()" variant="danger m-1" size="sm" block>
                                <i class="i-Power-2"></i>
                                {{ $t("Reset") }}
                            </b-button>
                        </b-col>
                    </b-row>
                </div>
            </b-sidebar>

            <!-- Modal Show Import Products -->
            <b-modal
                ok-only
                ok-title="Cancel"
                size="md"
                id="importProducts"
                :title="$t('import_products')"
            >
                <b-form @submit.prevent="Submit_import" enctype="multipart/form-data">
                    <b-row>
                        <!-- File -->
                        <b-col md="12" sm="12" class="mb-3">
                            <b-form-group>
                                <input type="file" @change="onFileSelected" label="Choose File">
                                <b-form-invalid-feedback
                                    id="File-feedback"
                                    class="d-block"
                                >{{$t('field_must_be_in_csv_format')}}</b-form-invalid-feedback>
                            </b-form-group>
                        </b-col>

                        <b-col md="6" sm="12">
                            <b-button type="submit" variant="primary" :disabled="ImportProcessing" size="sm" block>{{ $t("submit") }}</b-button>
                            <div v-once class="typo__p" v-if="ImportProcessing">
                                <div class="spinner sm spinner-primary mt-3"></div>
                            </div>
                        </b-col>

                        <b-col md="6" sm="12">
                            <a
                                :href="'/import/exemples/import_products.csv'"
                                class="btn btn-info btn-sm btn-block"

                            >{{ $t("Download_exemple") }}</a>
                        </b-col>

                        <b-col md="12" sm="12">
                            <table class="table table-bordered table-sm mt-4">
                                <tbody>
                                <tr>
                                    <td>{{$t('Name_product')}}</td>
                                    <th>
                                        <span class="badge badge-outline-success">{{$t('Field_is_required')}}</span>
                                    </th>
                                </tr>

                                <tr>
                                    <td>{{$t('CodeProduct')}}</td>
                                    <th>
                                        <span class="badge badge-outline-success">{{$t('Field_is_required')}}</span>
                                        {{$t('code_must_be_not_exist_already')}}
                                    </th>
                                </tr>

                                <tr>
                                    <td>{{$t('Categorie')}}</td>
                                    <th>
                                        <span class="badge badge-outline-success">{{$t('Field_is_required')}}</span>
                                    </th>
                                </tr>

                                <tr>
                                    <td>{{$t('ProductCost')}}</td>
                                    <th>
                                        <span class="badge badge-outline-success">{{$t('Field_is_required')}}</span>
                                    </th>
                                </tr>

                                <tr>
                                    <td>{{$t('ProductPrice')}}</td>
                                    <th>
                                        <span class="badge badge-outline-success">{{$t('Field_is_required')}}</span>
                                    </th>
                                </tr>

                                <tr>
                                    <td>{{$t('UnitProduct')}}</td>
                                    <th>
                                        <span class="badge badge-outline-success">{{$t('Field_is_required')}}</span>
                                        {{$t('must_be_exist')}} {{$t('Please_use_short_name_of_unit')}}
                                    </th>
                                </tr>

                                <tr>
                                    <td>{{$t('Brand')}}</td>
                                    <th>
                                        <span class="badge badge-outline-info">{{$t('Field_optional')}}</span>
                                    </th>
                                </tr>

                                <tr>
                                    <td>{{$t('StockAlert')}}</td>
                                    <th>
                                        <span class="badge badge-outline-info">{{$t('Field_optional')}}</span>
                                    </th>
                                </tr>

                                <tr>
                                    <td>{{$t('Note')}}</td>
                                    <th>
                                        <span class="badge badge-outline-info">{{$t('Field_optional')}}</span>
                                    </th>
                                </tr>
                                </tbody>
                            </table>
                        </b-col>
                    </b-row>
                </b-form>
            </b-modal>
        </div>
    </div>
</template>


<script>
import { mapActions, mapGetters } from "vuex";
import NProgress from "nprogress";
import jsPDF from "jspdf";
import "jspdf-autotable";

export default {
    metaInfo: {
        title: "Products"
    },

    data() {
        return {
            serverParams: {
                sort: {
                    field: "id",
                    type: "desc"
                },
                page: 1,
                perPage: 10
            },
            selectedIds: [],
            ImportProcessing:false,
            data: new FormData(),
            import_products: "",
            search: "",
            totalRows: "",
            isLoading: true,
            spinner: false,
            limit: "10",
            Filter_brand: "",
            Filter_code: "",
            Filter_name: "",
            Filter_category: "",
            categories: [],
            brands: [],
            products: {},
            pdfproducts: {},
            warehouses: [],
            totalsAllCount: 0,
            totalsAllQty: 0,
            totalsAllCost: 0,
            totalsAllPrice: 0,
            totalsAllProductSumma:0,
            totalsLoading: false,
        };
    },

    computed: {
        ...mapGetters(["currentUserPermissions"]),
        excelColumns(){
            return [
                {
                    label: this.$t("Name_product"),
                    field: "name",
                    html: true,
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Code"),
                    field: "code",
                    tdClass: "text-left",
                    thClass: "text-left"
                },

                {
                    label: this.$t("Brand"),
                    field: "brand",
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Categorie"),
                    field: "category",
                    tdClass: "text-left",
                    thClass: "text-left"
                },

                {
                    label: this.$t("Cost"),
                    field: "cost",
                    html: true,
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Price"),
                    field: "price",
                    html: true,
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Unit"),
                    field: "unit",
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Quantity"),
                    field: "quantity",
                    tdClass: "text-left",
                    thClass: "text-left"
                },
            ];
        },
        columns() {
            return [
                {
                    label: this.$t("image"),
                    field: "image",
                    type: "image",
                    html: true,
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("type"),
                    field: "type",
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Name_product"),
                    field: "name",
                    html: true,
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Code"),
                    field: "code",
                    tdClass: "text-left",
                    thClass: "text-left"
                },

                {
                    label: this.$t("Brand"),
                    field: "brand",
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Categorie"),
                    field: "category",
                    tdClass: "text-left",
                    thClass: "text-left"
                },

                {
                    label: this.$t("Cost"),
                    field: "cost",
                    html: true,
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Price"),
                    field: "price",
                    html: true,
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Unit"),
                    field: "unit",
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Quantity"),
                    field: "quantity",
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Action"),
                    field: "actions",
                    html: true,
                    tdClass: "text-right",
                    thClass: "text-right",
                    sortable: false
                }
            ];
        },
        // Data for Excel export with totals row appended
        excelData() {
            const rows = Array.isArray(this.products) ? this.products.map(r => ({
                ...r,
            })) : [];
            const totalsRow = {
                image: '',
                name: 'JAMI',
                code: '',
                category: '',
                quantity: this.totalsAllQty,
                price: this.totalsAllPrice,
                blank: '',
            };
            return [...rows, totalsRow];
        },
        // Totals for vue-good-table (current displayed rows)
        totalCount() {
            return Array.isArray(this.products) ? this.products.length : 0;
        },
        totalQty() {
            if (!Array.isArray(this.products)) return 0;
            const parse = (v) => {
                if (v === null || v === undefined) return 0;
                const n = parseFloat(String(v).replace(/<[^>]*>/g, '').replace(/[^\d.-]/g, ''));
                return isNaN(n) ? 0 : n;
            };
            return this.products.reduce((sum, r) => sum + parse(r.quantity), 0);
        },
        totalPrice() {
            if (!Array.isArray(this.products)) return 0;
            const parse = (v) => {
                if (v === null || v === undefined) return 0;
                const n = parseFloat(String(v).replace(/<[^>]*>/g, '').replace(/[^\d.-]/g, ''));
                return isNaN(n) ? 0 : n;
            };
            return this.products.reduce((sum, r) => sum + parse(r.price), 0);
        },
        totalQtyFormatted() {
            return this.totalQty.toLocaleString(undefined, { maximumFractionDigits: 2 });
        },
        totalPriceFormatted() {
            return this.totalPrice.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        // Formatted totals (ALL filtered products)
        totalsAllQtyFormatted() {
            return this.totalsAllQty.toLocaleString(undefined, { maximumFractionDigits: 2 });
        },
        totalsAllPriceFormatted() {
            return this.totalsAllPrice.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        totalsAllCostFormatted() {
            return this.totalsAllCost.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        totalsAllProductSummaFormatted() {
            return this.totalsAllProductSumma.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
    },
    methods: {
        //-------------------------------------- Products PDF ------------------------------\\
        async Product_PDF() {
            const self = this;
            const pdf = new jsPDF("p", "pt");

            const fontPath = "/fonts/Vazirmatn-Bold.ttf";
            pdf.addFont(fontPath, "VazirmatnBold", "bold");
            pdf.setFont("VazirmatnBold");

            // Columns with Image as the first column
            const columns = [
                { title: self.$t("Image"), dataKey: "image" },
                { title: "Tovar nomi", dataKey: "name" },
                { title: "Tovar kodi", dataKey: "code" },
                { title: "Kategoriya", dataKey: "category" },
                { title: "Narxi", dataKey: "price" },
                { title: "", dataKey: "blank" }
            ];

            // Helper to load image and convert to dataURL
            const toDataUrl = (url, size = 60) =>
                new Promise((resolve) => {
                    if (!url) return resolve(null);
                    const img = new Image();
                    img.crossOrigin = "anonymous";
                    img.onload = function () {
                        try {
                            const canvas = document.createElement("canvas");
                            const ctx = canvas.getContext("2d");
                            const w = size, h = size;
                            canvas.width = w;
                            canvas.height = h;
                            const scale = Math.max(w / img.width, h / img.height);
                            const x = (w / 2) - (img.width / 2) * scale;
                            const y = (h / 2) - (img.height / 2) * scale;
                            ctx.drawImage(img, x, y, img.width * scale, img.height * scale);
                            resolve(canvas.toDataURL("image/png"));
                        } catch (e) {
                            resolve(null);
                        }
                    };
                    img.onerror = () => resolve(null);
                    img.src = url;
                });

            // Prepare rows with image data
            let products_pdf = [];
            try {
                if (Array.isArray(self.pdfproducts) && self.pdfproducts.length > 0) {
                    products_pdf = await Promise.all(
                        JSON.parse(JSON.stringify(self.pdfproducts))
                            .filter(item => item && typeof item === 'object')
                            .sort((a, b) => {
                                const catA = (a.category || '').toString();
                                const catB = (b.category || '').toString();
                                return catA.localeCompare(catB);
                            })
                            .map(async (item) => {
                                try {
                                    const imageUrl = item.image ? `/images/products/${item.image}` : null;
                                    const imageData = await toDataUrl(imageUrl, 36);
                                    return {
                                        ...item,
                                        _img: imageData,
                                        image: null,
                                        name: item.name ? String(item.name).replace(/<br\s*\/?>/gi, "\n") : "",
                                        price: item.price ? String(item.price).replace(/<br\s*\/?>/gi, "\n") : "",
                                        blank: "",
                                    };
                                } catch (e) {
                                    console.error('Error processing product:', e);
                                    return null;
                                }
                            })
                    );
                    products_pdf = products_pdf.filter(item => item !== null);
                } else {
                    console.warn('No products data available for PDF generation');
                }
            } catch (error) {
                console.error('Error preparing products for PDF:', error);
            }

            // Calculate total price
            const toNumber = (v) => {
                if (v === null || v === undefined) return 0;
                const n = parseFloat(String(v).replace(/[^\d.-]/g, ''));
                return isNaN(n) ? 0 : n;
            };

            const totalPrice = products_pdf.reduce((sum, r) => sum + toNumber(r.price), 0);

            // Add title and date
            pdf.setFont("VazirmatnBold");
            pdf.setFontSize(18);
            pdf.text("Mahsulotlar ro'yxati", 40, 40);

            const currentDate = new Date().toLocaleDateString('uz-UZ');
            pdf.setFontSize(10);
            pdf.text(`Sana: ${currentDate}`, 40, 60);

            // Table config
            const tableConfig = {
                columns: columns,
                body: products_pdf,
                startY: 80,
                theme: "grid",
                didParseCell: (data) => {
                    if (data.section === "body" && data.column.dataKey === "image") {
                        data.cell.text = [];
                    }
                },
                headStyles: {
                    fillColor: [200, 200, 200],
                    textColor: [0, 0, 0],
                    fontStyle: "bold",
                },
                bodyStyles: {
                    valign: "middle",
                },
                didDrawCell: (data) => {
                    if (data.section === "body" && data.column.dataKey === "image") {
                        const imgData = data.row.raw?._img;
                        if (imgData) {
                            const size = 28;
                            const x = data.cell.x + 4;
                            const y = data.cell.y + 4;
                            try {
                                pdf.addImage(imgData, "PNG", x, y, size, size);
                            } catch (e) {
                                console.error('Error adding image:', e);
                            }
                        }
                    }
                },
                columnStyles: {
                    image: { cellWidth: 36, halign: "center", minCellHeight: 36, overflow: 'hidden' },
                    name: { cellWidth: 'auto', halign: 'left' },
                    code: { cellWidth: 'auto', halign: 'center' },
                    category: { cellWidth: 'auto', halign: 'center' },
                    price: { cellWidth: 'auto', halign: 'right' },
                    blank: { cellWidth: 36, halign: "center" },
                },
                margin: { top: 80, right: 20, bottom: 50, left: 20 },
            };

            // Generate table
            pdf.autoTable(tableConfig);

            // Add footer (total row) only on last page
            const finalY = pdf.lastAutoTable.finalY || 80;
            const footerY = finalY + 10;

            const foot = [
                [
                    { content: '', styles: { cellWidth: 36 } },
                    { content: 'Jami:', styles: { fontStyle: 'bold', halign: 'right', colSpan: 4 } },
                    { content: totalPrice.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' '), styles: { fontStyle: 'bold', halign: 'right', fillColor: [240, 240, 240] } },
                    { content: '', styles: { cellWidth: 36 } }
                ]
            ];

            pdf.autoTable({
                head: [],
                body: foot,
                startY: footerY,
                theme: 'plain',
                columnStyles: {
                    0: { cellWidth: 36 },
                    1: { halign: 'right', fontStyle: 'bold' },
                    2: { halign: 'right', fontStyle: 'bold', fillColor: [240, 240, 240] },
                    3: { cellWidth: 36 }
                },
                margin: { left: 20, right: 20 }
            });

            // Add page numbers
            const pageCount = pdf.internal.getNumberOfPages();
            for (let i = 1; i <= pageCount; i++) {
                pdf.setPage(i);
                pdf.setFontSize(10);
                pdf.text(
                    `Sahifa ${i} / ${pageCount}`,
                    pdf.internal.pageSize.width - 50,
                    pdf.internal.pageSize.height - 10,
                    { align: 'right' }
                );
            }

            // Save PDF
            const dateStr = new Date().toISOString().split('T')[0];
            pdf.save(`Mahsulotlar_${dateStr}.pdf`);
        },

        //----------------------------------- Show import products -------------------------------\\
        Show_import_products() {
            this.$bvModal.show("importProducts");
        },

        //------------------------------ Event Import products -------------------------------\\
        onFileSelected(e) {
            this.import_products = "";
            let file = e.target.files[0];
            let errorFilesize;

            if (file["size"] < 1048576) {
                // 1 mega = 1,048,576 Bytes
                errorFilesize = false;
            } else {
                this.makeToast(
                    "danger",
                    this.$t("file_size_must_be_less_than_1_mega"),
                    this.$t("Failed")
                );
            }

            if (errorFilesize === false) {
                this.import_products = file;
            }
        },

        //----------------------------------------Submit  import products-----------------\\
        Submit_import() {
            // Start the progress bar.
            NProgress.start();
            NProgress.set(0.1);
            var self = this;
            self.ImportProcessing = true;
            self.data.append("products", self.import_products);
            axios
                .post("products/import/csv", self.data)
                .then(response => {
                    self.ImportProcessing = false;
                    if (response.data.status === true) {
                        this.makeToast(
                            "success",
                            this.$t("Successfully_Imported"),
                            this.$t("Success")
                        );
                        Fire.$emit("Event_import");
                    } else if (response.data.status === false) {
                        this.makeToast(
                            "danger",
                            this.$t("field_must_be_in_csv_format"),
                            this.$t("Failed")
                        );
                    }
                    // Complete the animation of theprogress bar.
                    NProgress.done();
                })
                .catch(error => {
                    self.ImportProcessing = false;
                    // Complete the animation of theprogress bar.
                    NProgress.done();
                    if (error.response && error.response.status === 422) {
                        var errors = error.response.data.errors;

                        this.makeToast(
                            "danger",
                            errors,
                            this.$t("Failed")
                        );

                        // Display validation errors to user
                    } else {
                        this.makeToast(
                            "danger",
                            this.$t("Please_follow_the_import_instructions"),
                            this.$t("Failed")
                        );
                    }

                });
        },


        //------ Toast
        makeToast(variant, msg, title) {
            this.$root.$bvToast.toast(msg, {
                title: title,
                variant: variant,
                solid: true
            });
        },

        //----Update Params Table
        updateParams(newProps) {
            this.serverParams = Object.assign({}, this.serverParams, newProps);
        },

        //---- Event Page Change
        onPageChange({ currentPage }) {
            if (this.serverParams.page !== currentPage) {
                this.updateParams({ page: currentPage });
                this.Get_Products(currentPage);
            }
        },

        //---- Event Per Page Change
        onPerPageChange({ currentPerPage }) {
            if (this.limit !== currentPerPage) {
                this.limit = currentPerPage;
                this.updateParams({ page: 1, perPage: currentPerPage });
                this.Get_Products(1);
            }
        },

        //---- Event Select Rows
        selectionChanged({ selectedRows }) {
            this.selectedIds = [];
            selectedRows.forEach((row, index) => {
                this.selectedIds.push(row.id);
            });
        },

        //---- Event Sort Change
        onSortChange(params) {
            let field = "";
            if (params[0].field == "brand") {
                field = "brand_id";
            } else if (params[0].field == "category") {
                field = "category_id";
            } else {
                field = params[0].field;
            }
            this.updateParams({
                sort: {
                    type: params[0].type,
                    field: field
                }
            });
            this.Get_Products(this.serverParams.page);
        },

        //---- Event Search
        onSearch(value) {
            this.search = value.searchTerm;
            this.Get_Products(this.serverParams.page);
        },

        //------ Reset Filter
        Reset_Filter() {
            this.search = "";
            this.Filter_brand = "";
            this.Filter_code = "";
            this.Filter_name = "";
            this.Filter_category = "";
            this.Get_Products(this.serverParams.page);
        },

        // Simply replaces null values with strings=''
        setToStrings() {
            if (this.Filter_category === null) {
                this.Filter_category = "";
            } else if (this.Filter_brand === null) {
                this.Filter_brand = "";
            }
        },

        //----------------------------------- Get All Products ------------------------------\\
        Get_Products(page) {
            // Start the progress bar.
            NProgress.start();
            NProgress.set(0.1);
            this.setToStrings();
            axios
                .get(
                    "products?page=" +
                    page +
                    "&code=" +
                    this.Filter_code +
                    "&name=" +
                    this.Filter_name +
                    "&category_id=" +
                    this.Filter_category +
                    "&brand_id=" +
                    this.Filter_brand +
                    "&SortField=" +
                    this.serverParams.sort.field +
                    "&SortType=" +
                    this.serverParams.sort.type +
                    "&search=" +
                    this.search +
                    "&limit=" +
                    this.limit
                )
                .then(response => {
                    this.products = response.data.products;
                    this.warehouses = response.data.warehouses;
                    this.categories = response.data.categories;
                    this.brands = response.data.brands;
                    this.totalRows = response.data.totalRows;

                    // Complete the animation of theprogress bar.
                    NProgress.done();
                    this.isLoading = false;

                    // Also refresh totals for ALL filtered records
                    this.Fetch_Totals();
                })
                .catch(response => {
                    // Complete the animation of theprogress bar.
                    NProgress.done();
                    setTimeout(() => {
                        this.isLoading = false;
                    }, 500);
                });
        },

        // Fetch totals for ALL filtered products (independent of pagination)
        Fetch_Totals() {
            this.totalsLoading = true;
            const parse = (v) => {
                if (v === null || v === undefined) return 0;
                const n = parseFloat(String(v).replace(/<[^>]*>/g, '').replace(/[^\d.-]/g, ''));
                return isNaN(n) ? 0 : n;
            };
            axios
                .get(
                    "get_products"
                )
                .then(res => {
                    this.pdfproducts=res.data.products;
                    const rows = Array.isArray(res.data.products) ? res.data.products : [];


                    this.totalsAllCount = rows.length;
                    this.totalsAllQty = rows.reduce((s, r) => s + parse(r.quantity), 0);
                    this.totalsAllPrice = rows.reduce((s, r) => s + parse(r.price), 0);
                    this.totalsAllCost = rows.reduce((s, r) => s + parse(r.cost), 0);
                    this.totalsAllProductSumma = rows.reduce((s, r) => s + parse(r.price)*parse(r.quantity),0);
                })
                .finally(() => {
                    this.totalsLoading = false;
                });
        },

        //----------------------------------- Remove Product ------------------------------\\
        Remove_Product(id) {
            this.$swal({
                title: this.$t("Delete_Title"),
                text: this.$t("Delete_Text"),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                cancelButtonText: this.$t("Delete_cancelButtonText"),
                confirmButtonText: this.$t("Delete_confirmButtonText")
            }).then(result => {
                if (result.value) {
                    // Start the progress bar.
                    NProgress.start();
                    NProgress.set(0.1);
                    axios
                        .delete("products/" + id)
                        .then(() => {
                            this.$swal(
                                this.$t("Delete_Deleted"),
                                this.$t("Deleted_in_successfully"),
                                "success"
                            );

                            Fire.$emit("Delete_Product");
                        })
                        .catch(() => {
                            // Complete the animation of theprogress bar.
                            setTimeout(() => NProgress.done(), 500);
                            this.$swal(
                                this.$t("Delete_Failed"),
                                this.$t("Delete.AlreadyLinked"),
                                "warning"
                            );
                        });
                }
            });
        },

        //---- Delete products by selection
        delete_by_selected() {
            this.$swal({
                title: this.$t("Delete_Title"),
                text: this.$t("Delete_Text"),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                cancelButtonText: this.$t("Delete_cancelButtonText"),
                confirmButtonText: this.$t("Delete_confirmButtonText")
            }).then(result => {
                if (result.value) {
                    // Start the progress bar.
                    NProgress.start();
                    NProgress.set(0.1);
                    axios
                        .post("products/delete/by_selection", {
                            selectedIds: this.selectedIds
                        })
                        .then(() => {
                            this.$swal(
                                this.$t("Delete_Deleted"),
                                this.$t("Deleted_in_successfully"),
                                "success"
                            );

                            Fire.$emit("Delete_Product");
                        })
                        .catch(() => {
                            // Complete the animation of theprogress bar.
                            setTimeout(() => NProgress.done(), 500);
                            this.$swal(
                                this.$t("Delete_Failed"),
                                this.$t("Delete_Therewassomethingwronge"),
                                "warning"
                            );
                        });
                }
            });
        }
    }, //end Methods

    //-----------------------------Created function-------------------

    created: function() {
        this.Get_Products(1);

        Fire.$on("Delete_Product", () => {
            this.Get_Products(this.serverParams.page);
            // Complete the animation of theprogress bar.
            setTimeout(() => NProgress.done(), 500);
        });

        Fire.$on("Event_import", () => {
            setTimeout(() => {
                this.Get_Products(this.serverParams.page);
                this.$bvModal.hide("importProducts");
            }, 500);
        });
    }
};
</script>
