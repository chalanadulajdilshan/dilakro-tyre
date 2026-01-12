jQuery(document).ready(function ($) {
  const CONFIG = {
    CHEQUE_NO_REGEX: /^\d{6,12}$/,
    MIN_AMOUNT: 0.01,
    SWAL_TIMEOUT: 3000,
  };

  const state = {
    chequeInfo: [],
    cashAmount: 0,
    chequeTotal: 0,
  };

  function formatAmount(amount) {
    return parseFloat(amount || 0).toLocaleString("en-US", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  }

  function parseAmount(value) {
    let num = parseFloat(value.toString().replace(/,/g, ""));
    return isNaN(num) ? 0 : num;
  }

  function isValidChequeNo(chequeNo) {
    return CONFIG.CHEQUE_NO_REGEX.test(chequeNo);
  }

  function isValidDate(dateStr) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const inputDate = new Date(dateStr);
    return !isNaN(inputDate.getTime()) && inputDate >= today;
  }

  function updateTotals() {
    state.cashAmount = parseAmount($("#cash_amount").val());
    state.chequeTotal = 0;

    state.chequeInfo.forEach((cheque) => {
      state.chequeTotal += parseAmount(cheque.amount);
    });

    $("#cheque_total").val(formatAmount(state.chequeTotal));
    $("#total_amount").val(formatAmount(state.cashAmount + state.chequeTotal));
  }

  $("#add_cheque").on("click", function () {
    const chequeNo = $("#cheque_no").val().trim();
    const chequeDate = $("#cheque_date").val().trim();
    const bankBranch = $("#bank_branch_name").val().trim();
    const bankBranchId = $("#bank_branch").val().trim();
    const bankId = $("#bank_id").val().trim();
    const amount = parseAmount($("#amount").val());

    if (!isValidChequeNo(chequeNo)) {
      return swal(
        "Invalid Cheque Number",
        "Cheque number should be 6–12 digits.",
        "error"
      );
    }
    if (!isValidDate(chequeDate)) {
      return swal(
        "Invalid Cheque Date",
        "Cheque date must be today or a future date.",
        "error"
      );
    }
    if (!bankBranch || !bankBranchId) {
      return swal(
        "Missing Bank",
        "Please select a valid Bank & Branch.",
        "error"
      );
    }
    if (amount <= 0) {
      return swal(
        "Invalid Amount",
        "Amount should be a number greater than 0.",
        "error"
      );
    }

    $("#noItemRow").remove();
    const chequeId = "cheque_" + Date.now();
    state.chequeInfo.push({
      id: chequeId,
      chequeNo,
      chequeDate,
      bankBranch,
      bankBranchId,
      bankId,
      amount,
    });

    const newRow = `
      <tr data-cheque-id="${chequeId}">
        <td>${chequeNo}</td>
        <td>${chequeDate}</td>
        <td>${bankBranch}</td>
        <td class="cheque-amount" data-amount="${amount}">${formatAmount(
      amount
    )}</td>
        <td><button type="button" class="btn btn-sm btn-danger remove-row">Remove</button></td>
      </tr>`;
    $("#chequeBody").append(newRow);
    updateTotals();

    $("#cheque_no, #cheque_date, #bank_branch_name, #bank_branch, #bank_id, #amount").val(
      ""
    );
  });

  $("#cheque_no, #cheque_date, #bank_branch_name, #amount").on(
    "keypress",
    function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        $("#add_cheque").click();
      }
    }
  );

  $("#chequeBody").on("click", ".remove-row", function () {
    const $row = $(this).closest("tr");
    const chequeId = $row.data("cheque-id");
    $row.remove();
    if (chequeId) {
      const index = state.chequeInfo.findIndex(
        (cheque) => cheque.id === chequeId
      );
      if (index > -1) {
        state.chequeInfo.splice(index, 1);
      }
    }
    if ($("#chequeBody tr").length === 0) {
      $("#chequeBody").append(
        `<tr id="noItemRow"><td colspan="5" class="text-center text-muted">No items added</td></tr>`
      );
    }
    updateTotals();
  });

  $(document).on("click", ".select-branch", function () {
    const branchId = $(this).data("id");
    const bankId = $(this).data("bankid");
    const bankBranchName = $(this).find("td:eq(2)").text();
    $("#bank_branch").val(branchId);
    $("#bank_id").val(bankId);
    $("#bank_branch_name").val(bankBranchName);
    $("#branch_master").modal("hide");
  });

  let supplierTableInitialized = false;

  const loadSupplierTable = () => {
    if (!supplierTableInitialized) {
      $("#supplierTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: "ajax/php/customer-master.php",
          type: "POST",
          data: { filter: true, category: 2 },
          dataSrc: (json) => json.data,
          error: (xhr) =>
            console.error("Server Error Response:", xhr.responseText),
        },
        columns: [
          { data: "key", title: "#ID" },
          { data: "code", title: "Code" },
          { data: "name", title: "Name" },
          { data: "mobile_number", title: "Mobile Number" },
          { data: "email", title: "Email" },
        ],
        order: [[0, "desc"]],
        pageLength: 100,
        createdRow: (row, data) => {
          $(row).addClass("cursor-pointer");
        },
      });

      supplierTableInitialized = true;
    } else {
      $("#supplierTable").DataTable().ajax.reload();
    }

    $("#supplierTable tbody")
      .off("click", "tr")
      .on("click", "tr", function () {
        const data = $("#supplierTable").DataTable().row(this).data();

        if (data) {
          $("#supplier_id").val(data.id);
          $("#supplier_code").val(data.code);
          $("#supplier_name").val(data.name);
          $("#supplierModal").modal("hide");
        }
      });
  };

  $("#supplierModal").on("show.bs.modal", function () {
    loadSupplierTable();
  });

  $("#cash_amount").on("input", function () {
    updateTotals();
  });

  $("#new").click(function (e) {
    e.preventDefault();
    location.reload();
  });

  $("#create").click(function (event) {
    event.preventDefault();

    if (!$("#payment_no").val()) {
      return swal({
        title: "Error!",
        text: "Please enter payment number",
        type: "error",
        timer: CONFIG.SWAL_TIMEOUT,
        showConfirmButton: false,
      });
    }
    if (!$("#supplier_code").val()) {
      return swal({
        title: "Error!",
        text: "Please select a supplier",
        type: "error",
        timer: CONFIG.SWAL_TIMEOUT,
        showConfirmButton: false,
      });
    }
    if (!$("#entry_date").val()) {
      return swal({
        title: "Error!",
        text: "Please select an entry date",
        type: "error",
        timer: CONFIG.SWAL_TIMEOUT,
        showConfirmButton: false,
      });
    }

    const totalAmount = parseAmount($("#total_amount").val());
    if (totalAmount <= 0) {
      return swal({
        title: "Error!",
        text: "Total amount must be greater than 0",
        type: "error",
        timer: CONFIG.SWAL_TIMEOUT,
        showConfirmButton: false,
      });
    }

    $(".someBlock").preloader();

    const chequeDetails = state.chequeInfo.map((cheque) => ({
      cheque_no: cheque.chequeNo,
      cheque_date: cheque.chequeDate,
      bank_id: cheque.bankId,
      branch_id: cheque.bankBranchId,
      amount: cheque.amount,
    }));

    const formData = new FormData($("#form-data")[0]);
    formData.append("supplier_id", $("#supplier_id").val());
    formData.append("cash_amount", parseAmount($("#cash_amount").val()));
    formData.append("cheque_amount", state.chequeTotal);
    formData.append("total_amount", totalAmount);
    formData.append("cheque_details", JSON.stringify(chequeDetails));
    formData.append("create", true);

    $.ajax({
      url: "ajax/php/supplier-payment-new.php",
      type: "POST",
      data: formData,
      async: false,
      cache: false,
      contentType: false,
      processData: false,
      success: function (result) {
        $(".someBlock").preloader("remove");
        if (result.status === "success") {
          swal({
            title: "Success!",
            text: "Supplier payment created successfully!",
            type: "success",
            timer: CONFIG.SWAL_TIMEOUT,
            showConfirmButton: false,
          });
          setTimeout(() => window.location.reload(), CONFIG.SWAL_TIMEOUT);
        } else {
          swal({
            title: "Error!",
            text: result.message || "Something went wrong.",
            type: "error",
            timer: CONFIG.SWAL_TIMEOUT,
            showConfirmButton: false,
          });
        }
      },
      error: function (xhr) {
        $(".someBlock").preloader("remove");
        swal(
          "Error",
          "Failed to create supplier payment. Please try again.",
          "error"
        );
      },
    });
  });

  updateTotals();
});
