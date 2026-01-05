jQuery(document).ready(function () {

  // CREATE Location
  $("#create").click(function (event) {
    event.preventDefault();

    if (!$("#code").val()) {
      swal({
        title: "Error!",
        text: "Please enter Location Code",
        type: "error",
        timer: 2000,
        showConfirmButton: false,
      });
      return;
    }

    if (!$("#name").val()) {
      swal({
        title: "Error!",
        text: "Please enter Location Name",
        type: "error",
        timer: 2000,
        showConfirmButton: false,
      });
      return;
    }

    $(".someBlock").preloader();

    var formData = new FormData($("#form-data")[0]);
    formData.append("create", true);

    $.ajax({
      url: "ajax/php/location-master.php",
      type: "POST",
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (result) {
        $(".someBlock").preloader("remove");

        if (result.status === "success") {
          swal({
            title: "Success!",
            text: "Location added successfully!",
            type: "success",
            timer: 2000,
            showConfirmButton: false,
          });

          setTimeout(() => {
            window.location.reload();
          }, 2000);
        } else {
          swal({
            title: "Error!",
            text: "Something went wrong.",
            type: "error",
            timer: 2000,
            showConfirmButton: false,
          });
        }
      },
    });
  });

  // UPDATE Location
  $("#update").click(function (event) {
    event.preventDefault();

    if (!$("#code").val()) {
      swal({
        title: "Error!",
        text: "Please enter Location Code",
        type: "error",
        timer: 2000,
        showConfirmButton: false,
      });
      return;
    }

    if (!$("#name").val()) {
      swal({
        title: "Error!",
        text: "Please enter Location Name",
        type: "error",
        timer: 2000,
        showConfirmButton: false,
      });
      return;
    }

    $(".someBlock").preloader();

    var formData = new FormData($("#form-data")[0]);
    formData.append("update", true);

    $.ajax({
      url: "ajax/php/location-master.php",
      type: "POST",
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (result) {
        $(".someBlock").preloader("remove");

        if (result.status === "success") {
          swal({
            title: "Success!",
            text: "Location updated successfully!",
            type: "success",
            timer: 2000,
            showConfirmButton: false,
          });

          setTimeout(() => {
            window.location.reload();
          }, 2000);
        } else {
          swal({
            title: "Error!",
            text: "Something went wrong.",
            type: "error",
            timer: 2000,
            showConfirmButton: false,
          });
        }
      },
    });
  });

  // DELETE Location
  $(document).on("click", ".delete-location-master", function (e) {
    e.preventDefault();

    const id = $("#id").val();
    const name = $("#name").val();

    if (!id) {
      swal({
        title: "Error!",
        text: "Please select a Location first.",
        type: "error",
        timer: 2000,
        showConfirmButton: false,
      });
      return;
    }

    swal(
      {
        title: "Are you sure?",
        text: "Do you want to delete '" + name + "'?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
        closeOnConfirm: false,
      },
      function (isConfirm) {
        if (isConfirm) {
          $(".someBlock").preloader();

          $.ajax({
            url: "ajax/php/location-master.php",
            type: "POST",
            data: {
              id: id,
              delete: true,
            },
            dataType: "json",
            success: function (response) {
              $(".someBlock").preloader("remove");

              if (response.status === "success") {
                swal({
                  title: "Deleted!",
                  text: "Location has been deleted.",
                  type: "success",
                  timer: 2000,
                  showConfirmButton: false,
                });

                setTimeout(() => {
                  window.location.reload();
                }, 2000);
              } else {
                swal({
                  title: "Error!",
                  text: "Something went wrong.",
                  type: "error",
                  timer: 2000,
                  showConfirmButton: false,
                });
              }
            },
          });
        }
      }
    );
  });

  // NEW button (reset form)
  $("#new").click(function (e) {
    e.preventDefault();

    $("#form-data")[0].reset();
    $("#id").val("");

    $("#create").show();
    $("#update").hide();
    $(".delete-location-master").hide();
  });

  // SELECT Location from modal/table
  $(document).on("click", ".select-location", function () {
    $("#id").val($(this).data("id"));
    $("#code").val($(this).data("code"));
    $("#name").val($(this).data("name"));
    $("#is_active").prop("checked", $(this).data("is_active") == 1);

    $("#create").hide();
    $("#update").show();
    $(".delete-location-master").show();

    $(".bs-example-modal-xl").modal("hide");
  });

});
