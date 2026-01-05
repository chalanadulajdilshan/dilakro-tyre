// Overrides for public live stock page without touching the core live-stock.js
jQuery(function () {
  function applyPublicView(table) {
    if (!table) return;

    // Ensure all columns start visible before applying public hides
    if (table.columns && typeof table.columns === "function") {
      table.columns().every(function () {
        this.visible(true);
      });
    }

    // Hide columns: 0 details toggle, 1 item code, 3 department, 4 category, 10 stock status
    [0, 1, 3, 4, 9].forEach(function (idx) {
      if (table.column(idx)) table.column(idx).visible(false, false);
    });
    // Force visible columns (description, group, list, selling, qty)
    [2, 5, 6, 7, 8].forEach(function (idx) {
      if (table.column(idx)) table.column(idx).visible(true, false);
    });
    table.columns.adjust().draw(false);

    // Disable ARN expand handler
    $("#stockTable tbody").off("click", "td.details-control");

    // Disable DataTables responsive child rows and controls for public view
    if (table.responsive && typeof table.responsive.disable === "function") {
      table.responsive.disable();
    }
    // Remove responsive control classes and click handlers that add + icons
    const $table = $("#stockTable");
    $table.removeClass("dtr-inline collapsed");
    $table.off("click", "tbody td.dtr-control");

    // Mobile readability: smaller font, single-line cells, keep horizontal scroll
    if (!document.getElementById("public-stock-mobile-style")) {
      const style = document.createElement("style");
      style.id = "public-stock-mobile-style";
      style.textContent = `
        #stockTable {
          font-size: 7px;
          white-space: nowrap;
          table-layout: auto;
          min-width: 100%;
        }
        #stockTable td, #stockTable th { 
          padding: 2px 3px;
          font-size: 7px;
        }
        /* Column widths - adjusted for mobile to show all columns including Quantity */
        #stockTable th:nth-child(3), #stockTable td:nth-child(3) { 
          width: 28%; 
          max-width: 120px;
          overflow: hidden;
          text-overflow: ellipsis;
        } /* Item Description */
        #stockTable th:nth-child(6), #stockTable td:nth-child(6) { width: 15%; } /* Group */
        #stockTable th:nth-child(7), #stockTable td:nth-child(7) { width: 16%; } /* List Price */
        #stockTable th:nth-child(8), #stockTable td:nth-child(8) { width: 16%; } /* Selling Price */
        #stockTable th:nth-child(9), #stockTable td:nth-child(9) { 
          width: 13%; 
          text-align: right;
        } /* Quantity */
        #stockTable_wrapper .table-responsive { 
          overflow-x: auto;
          -webkit-overflow-scrolling: touch;
        }
        @media (max-width: 768px) {
          #stockTable {
            font-size: 7px;
          }
          #stockTable td, #stockTable th {
            font-size: 7px;
            padding: 2px 2px;
          }
        }
      `;
      document.head.appendChild(style);
    }
    $table.closest(".table-responsive").css("overflow-x", "auto");
  }

  function setup() {
    if (!$.fn.dataTable || !$.fn.dataTable.isDataTable("#stockTable")) {
      return;
    }
    const table = $("#stockTable").DataTable();
    applyPublicView(table);
  }

  // If DataTable is already initialized, apply immediately
  setup();

  // Also apply right after initialization in case this script loads first
  $("#stockTable").on("init.dt", function () {
    setup();
  });
});
