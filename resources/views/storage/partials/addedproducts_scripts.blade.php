<script>
// Search and filter functionality
document.getElementById('searchProduct').addEventListener('keyup', function() {
    filterTable();
});

document.getElementById('filterCategory').addEventListener('change', function() {
    filterTable();
});

function filterTable() {
    const searchValue = document.getElementById('searchProduct').value.toLowerCase();
    const categoryValue = document.getElementById('filterCategory').value;
    const rows = document.querySelectorAll('.product-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const productName = row.getAttribute('data-product-name');
        const categoryId = row.getAttribute('data-category-id');

        let showBySearch = productName.includes(searchValue);
        let showByCategory = categoryValue === '' || categoryId === categoryValue;

        if (showBySearch && showByCategory) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const noDataRow = document.getElementById('noDataRow');
    if (noDataRow) {
        noDataRow.style.display = visibleCount === 0 ? '' : 'none';
    }
}

// Excel export function
function exportToExcel() {
    const table = document.getElementById('productsTable');
    const rows = [];

    rows.push(['#', 'Maxsulot nomi', 'O\'lchov birligi', 'Kirim', 'Chiqim', 'Qoldiq']);

    const productRows = document.querySelectorAll('.product-row');
    let counter = 1;
    productRows.forEach(row => {
        if (row.style.display !== 'none') {
            const cols = row.querySelectorAll('td');
            const qoldiq = row.querySelector('.qoldiq-value').getAttribute('data-qoldiq');
            rows.push([
                counter++,
                cols[1].textContent.trim(),
                cols[2].textContent.trim(),
                cols[3].textContent.trim(),
                cols[4].textContent.trim(),
                qoldiq
            ]);
        }
    });

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(rows);

    ws['!cols'] = [
        {wch: 5},
        {wch: 40},
        {wch: 15},
        {wch: 15},
        {wch: 15},
        {wch: 15}
    ];

    XLSX.utils.book_append_sheet(wb, ws, 'Maxsulotlar qoldiqlari');

    const fileName = 'maxsulotlar_qoldiqlari_' + new Date().toISOString().slice(0,10) + '.xlsx';
    XLSX.writeFile(wb, fileName);
}

// PDF export function
function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'mm', 'a4');

    doc.setFont('helvetica');
    doc.setFontSize(16);
    doc.text('Maxsulotlar qoldiqlari hisoboti', 14, 15);

    doc.setFontSize(10);
    doc.text('Sana: ' + new Date().toLocaleDateString('uz-UZ'), 14, 22);

    const tableData = [];
    const productRows = document.querySelectorAll('.product-row');
    let counter = 1;

    productRows.forEach(row => {
        if (row.style.display !== 'none') {
            const cols = row.querySelectorAll('td');
            const qoldiq = row.querySelector('.qoldiq-value').getAttribute('data-qoldiq');
            tableData.push([
                counter++,
                cols[1].textContent.trim(),
                cols[2].textContent.trim(),
                cols[3].textContent.trim(),
                cols[4].textContent.trim(),
                qoldiq
            ]);
        }
    });

    doc.autoTable({
        head: [['#', 'Maxsulot nomi', 'O\'lchov birligi', 'Kirim', 'Chiqim', 'Qoldiq']],
        body: tableData,
        startY: 28,
        theme: 'grid',
        headStyles: {
            fillColor: [102, 126, 234],
            textColor: [255, 255, 255],
            fontSize: 10,
            fontStyle: 'bold'
        },
        columnStyles: {
            0: {cellWidth: 10, halign: 'center'},
            1: {cellWidth: 80},
            2: {cellWidth: 30, halign: 'center'},
            3: {cellWidth: 30, halign: 'center'},
            4: {cellWidth: 30, halign: 'center'},
            5: {cellWidth: 30, halign: 'center'}
        },
        didParseCell: function(data) {
            if (data.column.index === 5 && data.section === 'body') {
                const value = parseFloat(data.cell.raw);
                if (value > 0) {
                    data.cell.styles.textColor = [40, 167, 69];
                    data.cell.styles.fontStyle = 'bold';
                } else if (value < 0) {
                    data.cell.styles.textColor = [220, 53, 69];
                    data.cell.styles.fontStyle = 'bold';
                }
            }
        }
    });

    const fileName = 'maxsulotlar_qoldiqlari_' + new Date().toISOString().slice(0,10) + '.pdf';
    doc.save(fileName);
}

// Edit modal handler
$('.edite_, .icon-btn').click(function() {
    var id = $(this).attr('data-id');
    document.getElementById("group_id").value = id;
    var title = $(this).attr('data-title');
    document.getElementById("title").value = title;
    var dayid = $(this).attr('data-dayid');
    var options = document.getElementById("daySelect").options;
    for (var i = 0; i < options.length; i++) {
        if (options[i].value == dayid) {
            options[i].selected = true;
            break;
        }
    }
    var yearid = $(this).attr('data-yearid');
    var monthid = $(this).attr('data-monthid');

    document.getElementById("gyear_id").value = yearid;
    document.getElementById("gmonth_id").value = monthid;
});

// Delete modal handler
$('.detete').click(function() {
    var debtid = $(this).attr('data-debt-id');
    document.getElementById("ddebt_id").value = debtid;
});

// Modal helper function
function hideModal(t) {
    // Function placeholder for modal management
}

// Number validation
function isNumber(evt) {
    let charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;
    return true;
}

// Product management objects
var money = {
    current_income: $('#current-income'),
    current_balance: $('#current-balance'),
    current_expenses: $('#current-expenses'),
    balance: 0,
    income: 0,
    expenses: 0,
    update: function() {
        this.current_income.html(this.income);
        this.current_expenses.html(this.expenses);
        this.current_balance.html(this.balance);
    }
};

var r_money = {
    current_income: $('#current-income'),
    current_balance: $('#current-balance'),
    current_expenses: $('#current-expenses'),
    balance: 0,
    income: 0,
    expenses: 0,
    update: function() {
        this.current_income.html(this.income);
        this.current_expenses.html(this.expenses);
        this.current_balance.html(this.balance);
    }
};

var product = {};
var r_product = {};

// Product iteration
product.iterate = function() {
    product.items = $('#tablebody tr');
    money.income = 0;
    money.expenses = 0;

    product.items.each(function() {
        var this_row = $(this);
        $(this).find('td span input, td button').click(function() {
            product_delete_row(this_row);
        });

        var product_expense = parse_currency($(this).find('td')[1].innerHTML);
        var product_income = parse_currency($(this).find('td')[2].innerHTML);

        money.income += product_income;
        money.expenses += product_expense;
    });

    money.balance = money.income - money.expenses;
    money.update();
}

// Residual product iteration
r_product.iterate = function() {
    r_product.items = $('#table-body tr');
    r_money.income = 0;
    r_money.expenses = 0;

    r_product.items.each(function() {
        var this_row = $(this);
        $(this).find('td i, td button').click(function() {
            r_product_delete_row(this_row);
        });

        var product_expense = parse_currency($(this).find('td')[1].innerHTML);
        var product_income = parse_currency($(this).find('td')[2].innerHTML);

        r_money.income += product_income;
        r_money.expenses += product_expense;
    });

    r_money.balance = r_money.income - r_money.expenses;
    r_money.update();
}

// Initial call
product.iterate();
r_product.iterate();

// Add product handlers
$('#add-item').click(function() {
    add_product();
    product.iterate();
});

$('#additem').click(function() {
    r_add_product();
    r_product.iterate();
});

// Delete row functions
function product_delete_row(row) {
    row.remove();
    product.iterate();
}

function r_product_delete_row(row) {
    row.remove();
    r_product.iterate();
}

// Add product function
function add_product() {
    var row = $('<tr>')
        .append($('<td>').html($('#input-note-bar').find('option:selected').text() + "<input type='hidden' name='productsid[]' value=" + $('#input-note-bar').val() + ">"))
        .append($('<td>').html(get_expense_input() + "<input type='hidden' name='weights[]' value=" + get_expense_input() + ">"))
        .append($('<td>').html(get_income_input() + "<input type='hidden' name='costs[]' value=" + get_income_input() + ">"))
        .append($('<td>').html($('#get_shop_select').find('option:selected').text() + "<input type='hidden' name='shops[]' value=" + get_shop_select() + ">"))
        .append($('<td>').html(get_summa_input() + "<input type='hidden' name='pays[]' value=" + get_summa_input() + ">"))
        .append($('<td>').html('<button type="button" class="btn btn-sm btn-danger">Delete</button>'));

    var find = 0;
    $('#tablebody').find("td").each(function() {
        if ($(this).text() == $('#input-note-bar').find('option:selected').text()) {
            find = 1;
        }
    });
    if (get_expense_input() == "" || get_income_input() == "") {
        find = 1;
    }

    if (find == 0) {
        row.prependTo('#tablebody');
    }
}

// Add residual product function
function r_add_product() {
    var row = $('<tr>')
        .append($('<td>').html($('#input-notebar').find('option:selected').text() + "<input type='hidden' name='productsid[]' value=" + $('#input-notebar').val() + ">"))
        .append($('<td>').html(get_weight_input() + "<input type='hidden' name='weights[]' value=" + get_weight_input() + ">"))
        .append($('<td>').html(get_cost_input() + "<input type='hidden' name='costs[]' value=" + get_cost_input() + ">"))
        .append($('<td>').html('<button type="button" class="btn btn-sm btn-danger">Delete</button>'));

    var find = 0;
    $('#table-body').find("td").each(function() {
        if ($(this).text() == $('#input-notebar').find('option:selected').text()) {
            find = 1;
        }
    });

    if (find == 0) {
        row.prependTo('#table-body');
    }
}

// Input getter functions
function get_income_input() {
    return $('#input-income-bar').val() != "" ? $('#input-income-bar').val() : 0;
}

function get_cost_input() {
    return $('#input-incomebar').val() != "" ? $('#input-incomebar').val() : 0;
}

function get_expense_input() {
    return $('#input-expense-bar').val() != "" ? $('#input-expense-bar').val() : 0;
}

function get_weight_input() {
    return $('#input-expensebar').val() != "" ? $('#input-expensebar').val() : 0;
}

function get_summa_input() {
    return $('#input-summa-bar').val() != "" ? $('#input-summa-bar').val() : 0;
}

function get_shop_select() {
    return $('#get_shop_select').val() != "" ? $('#get_shop_select').val() : 0;
}

// Calculate payment
$("#input-summa-bar").click(function() {
    $("#input-summa-bar").val(get_expense_input() * get_income_input());
});

// Parse currency
function parse_currency(value) {
    return Number(parseFloat(value.replace(/[^0-9\.]+/g, "")));
}
</script>
