const tableHeaders = [{ key: "user", parser: "string" }];
const tableHeadersConfig = [{
    key: "user",
    formatter: "string",
    sortable: true,
    className: "table-head"
}];

var percentage = '';
var color = '';

function prova(Y, headers, userHead, totalHead, percentage, color) {
    tableHeadersConfig[0].label = userHead;
    tableHeaders.push({ key: 'total', parser: 'integer' });
    tableHeadersConfig.push({
        key: 'total',
        label: totalHead,
        formatter: "integer",
        className: "table-head"
    });
    for (const [key, val] of Object.entries(headers)) {
        const subHeaders = [];
        Object.keys(val).forEach(subHead => {
            tableHeaders.push({ key: `${key}${subHead}`, parser: 'string' });
            subHeaders.push({
                key: `${key}${subHead}`,
                label: `${subHead}`,
                formatter: "string",
                className: "section-cell"
            });
        });
        tableHeadersConfig.push({
            key: key,
            label: key,
            formatter: "string",
            children: subHeaders
        });
    }
    this.percentage = parseFloat(percentage);
    this.color = color;
}

YUI().use("yui2-datatable", "yui2-paginator", function (Y) {
    var YAHOO = Y.YUI2;
    var dataSource = new YAHOO.util.DataSource(YAHOO.util.Dom.
        get("users"));
    dataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
    dataSource.responseSchema = {
        fields: tableHeaders
    };
    var columns = tableHeadersConfig;
    var dataTable = new YAHOO.widget.DataTable
        ("container", columns, dataSource);
    changeColor();
    document.querySelector('[class*="yui-dt-sortable"]').addEventListener("click", () => { setTimeout(() => {
        changeColor()
    }, 50);  });
});

function changeColor() {
    console.log(this.color);
    let heads = document.querySelectorAll('[class*="table-head"]');
    let textColor = getTextColor(extractColor(this.color));
    heads.forEach(head => {
        element = head.querySelector('[class*="main"]');
        if (element != null) {
            let currentPercentage = parseFloat(element.innerHTML);
            if (currentPercentage < this.percentage) {
                element.style.backgroundColor = this.color;
                element.style.color = textColor;
                console.log(element);
            }
        }
    });
}

function extractColor(color) {
    let hex = color.substring(1);
    let red = hex.substring(0, 2);
    let green = hex.substring(2, 4);
    let blue = hex.substring(4, 6);
    return [Number('0x' + red), Number('0x' + green), Number('0x' + blue)];
}

function getTextColor(colors) {
    if ((colors[0] * 0.299 + colors[1] * 0.587 + colors[2] * 0.114) > 186) {
        return "#000000";
    } else {
        return "#FFFFFF";
    }
}
