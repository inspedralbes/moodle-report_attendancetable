const tableHeaders = [{ key: "user", parser: "string" }];
const tableHeadersConfig = [{
    key: "user",
    formatter: "string",
    sortable: true
}];

function prova(Y, headers, userHead, totalHead) {
    tableHeadersConfig[0].label = userHead;
    tableHeaders.push({ key: 'total', parser: 'integer' });
    tableHeadersConfig.push({
        key: 'total',
        label: totalHead,
        formatter: "integer",
    });
    for (const [key, val] of Object.entries(headers)) {
        const subHeaders = [];
        Object.keys(val).forEach(subHead => {
            tableHeaders.push({ key: `${key}${subHead}`, parser: 'string' });
            subHeaders.push({
                key: `${key}${subHead}`,
                label: `${subHead}`,
                formatter: "string",
            });
        });
        tableHeadersConfig.push({
            key: key,
            label: key,
            formatter: "string",
            children: subHeaders
        });
    }
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
});