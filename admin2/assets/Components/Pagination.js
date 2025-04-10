// class Pagination {
//     constructor() {
//         this.currentPage = 1;
//         this.totalItems = 0;
//         this.itemsPerPage = 5;
//         this.fetchDataFn = null;
//     }

//     init(fetchFn, perPage = 5) {
//         this.fetchDataFn = fetchFn;
//         this.itemsPerPage = perPage;
//         this.currentPage = 1;
//         this.fetchDataFn(this.currentPage, this.itemsPerPage);
//     }

//     updateRecordInfo(start, end, total) {
//         const recordInfo = document.getElementById('record-info');
//         if (recordInfo) {
//             recordInfo.textContent = `Đang hiển thị ${start}–${end} trên tổng số ${total} mục`;
//         }
//     }

//     render(total, containerId = 'pagination-container') {
//         this.totalItems = total;
//         const totalPages = Math.ceil(this.totalItems / this.itemsPerPage);
//         const container = document.getElementById(containerId);
//         if (!container || totalPages <= 1) return;

//         container.innerHTML = '';
//         const ul = document.createElement('ul');
//         ul.className = 'pagination';

//         // Previous
//         ul.appendChild(this.createPageItem('&laquo;', this.currentPage - 1, this.currentPage === 1));

//         // Pages
//         const startPage = Math.max(1, this.currentPage - 2);
//         const endPage = Math.min(totalPages, this.currentPage + 2);

//         if (startPage > 1) {
//             ul.appendChild(this.createPageItem(1, 1));
//             if (startPage > 2) ul.appendChild(this.createPageItem('...', 0, true));
//         }

//         for (let i = startPage; i <= endPage; i++) {
//             ul.appendChild(this.createPageItem(i, i, i === this.currentPage));
//         }

//         if (endPage < totalPages) {
//             if (endPage < totalPages - 1) ul.appendChild(this.createPageItem('...', 0, true));
//             ul.appendChild(this.createPageItem(totalPages, totalPages));
//         }

//         // Next
//         ul.appendChild(this.createPageItem('&raquo;', this.currentPage + 1, this.currentPage === totalPages));

//         container.appendChild(ul);
//     }

//     createPageItem(text, page, isDisabled = false) {
//         const li = document.createElement('li');
//         li.className = `page-item ${isDisabled ? 'disabled' : ''} ${page === this.currentPage ? 'active' : ''}`;

//         const a = document.createElement('a');
//         a.className = 'page-link';
//         a.href = '#';
//         a.innerHTML = text;

//         if (!isDisabled && page > 0) {
//             a.addEventListener('click', (e) => {
//                 e.preventDefault();
//                 this.currentPage = page;
//                 this.fetchDataFn(this.currentPage, this.itemsPerPage);
//             });
//         }

//         li.appendChild(a);
//         return li;
//     }
// }

// // Tạo instance toàn cục
// const pagination = new Pagination();

// // Export để sử dụng ở các file khác
// window.pagination = pagination;
// window.Pagination = Pagination;


class Pagination {
    constructor(defaultItemsPerPage = 5) {
        this.currentPage = 1;
        this.totalItems = 0;
        this.itemsPerPage = defaultItemsPerPage;
        this.filterFn = null;
        this.extraParams = "";
        this.searchValue = "";
    }

    init(filterFn, perPage = null, extraParams = "") {
        this.filterFn = filterFn;
        this.itemsPerPage = perPage || this.itemsPerPage; // Giữ nguyên giá trị mặc định nếu không truyền
        this.extraParams = extraParams;
        this.currentPage = 1;
        this.loadData();
    }

    loadData() {
        if (this.filterFn) {
            this.filterFn(
                this.searchValue,
                this.extraParams,
                this.currentPage,
                this.itemsPerPage
            );
        }
    }

    updateRecordInfo(start, end, total) {
        const recordInfo = document.getElementById('record-info');
        if (recordInfo) {
            recordInfo.textContent = `Đang hiển thị ${start}–${end} trên tổng số ${total} mục`;
        }
    }

    render(total, containerId = 'pagination-container') {
        this.totalItems = total;
        const totalPages = Math.ceil(this.totalItems / this.itemsPerPage);
        const container = document.getElementById(containerId);

        if (!container) {
            console.error("Không tìm thấy container phân trang");
            return;
        }

        container.innerHTML = '';

        if (totalPages <= 1) {
            return; // Ẩn phân trang nếu chỉ có 1 trang
        }

        const ul = document.createElement('ul');
        ul.className = 'pagination justify-content-center';

        // Nút Previous
        ul.appendChild(this.createPageItem(
            '&laquo;',
            this.currentPage - 1,
            this.currentPage === 1,
            'Trang trước'
        ));

        // Các trang
        const maxVisiblePages = 5; // Số trang hiển thị tối đa
        let startPage, endPage;

        if (totalPages <= maxVisiblePages) {
            startPage = 1;
            endPage = totalPages;
        } else {
            const maxPagesBeforeCurrent = Math.floor(maxVisiblePages / 2);
            const maxPagesAfterCurrent = Math.ceil(maxVisiblePages / 2) - 1;

            if (this.currentPage <= maxPagesBeforeCurrent) {
                startPage = 1;
                endPage = maxVisiblePages;
            } else if (this.currentPage + maxPagesAfterCurrent >= totalPages) {
                startPage = totalPages - maxVisiblePages + 1;
                endPage = totalPages;
            } else {
                startPage = this.currentPage - maxPagesBeforeCurrent;
                endPage = this.currentPage + maxPagesAfterCurrent;
            }
        }

        // Nút đầu tiên và dấu ... nếu cần
        if (startPage > 1) {
            ul.appendChild(this.createPageItem(1, 1));
            if (startPage > 2) {
                ul.appendChild(this.createPageItem('...', 0, true));
            }
        }

        // Các trang chính
        for (let i = startPage; i <= endPage; i++) {
            ul.appendChild(this.createPageItem(i, i, i === this.currentPage));
        }

        // Nút cuối cùng và dấu ... nếu cần
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                ul.appendChild(this.createPageItem('...', 0, true));
            }
            ul.appendChild(this.createPageItem(totalPages, totalPages));
        }

        // Nút Next
        ul.appendChild(this.createPageItem(
            '&raquo;',
            this.currentPage + 1,
            this.currentPage === totalPages,
            'Trang sau'
        ));

        container.appendChild(ul);
    }

    createPageItem(text, page, isDisabled = false, ariaLabel = '') {
        const li = document.createElement('li');
        li.className = `page-item ${isDisabled ? 'disabled' : ''} ${page === this.currentPage ? 'active' : ''}`;

        const a = document.createElement('a');
        a.className = 'page-link';
        a.href = '#';
        a.innerHTML = text;

        if (ariaLabel) {
            a.setAttribute('aria-label', ariaLabel);
        }

        if (!isDisabled && page > 0) {
            a.addEventListener('click', (e) => {
                e.preventDefault();
                this.currentPage = page;
                this.loadData();

                // Cuộn lên đầu trang khi chuyển trang
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        li.appendChild(a);
        return li;
    }

    // Thêm hàm để cập nhật search value
    setSearchValue(value) {
        this.searchValue = value;
        this.currentPage = 1; // Reset về trang 1 khi tìm kiếm
    }
}

// Tạo instance toàn cục
const pagination = new Pagination();

// Export để sử dụng ở các file khác
window.pagination = pagination;
window.Pagination = Pagination;