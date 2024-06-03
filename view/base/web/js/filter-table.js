/**
 * https://blog.pagesd.info/2019/10/01/search-filter-table-javascript/
 *
 */


'use strict';

class  FilterTable {

    constructor(tableNode) {
        this.tableNode = tableNode;

        this.input= document.createElement("input")
        this.input.setAttribute("type","search");//, placeholder:"search this table", name:""});
        this.input.addEventListener(
            "input",
            () => {
                this.onInputEvent(this.tableNode);
            },
            false,
        );

        let container = document.createElement("p");
        container.classList.add("filter-table");
        container.appendChild(document.createTextNode('Search filter: '));
        container.appendChild(this.input);
        tableNode.insertAdjacentElement('beforeBegin', container);
    }


    onInputEvent(table) {
        for (let row of table.rows)
        {
            this.filter(row)
        }
    }


    filter(row) {
        var text = row.textContent.toLowerCase();
        var val = this.input.value.toLowerCase();
        row.style.display = text.indexOf(val) === -1 ? 'none' : 'table-row';
    }

}
