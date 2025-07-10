$(document).ready(function () {

    requestGET()

    $("#finalizarUsuario").on("click", function () {
        editing.fullname = $("#nomeCompleto").val()
        editing.birthday = $("#dataNascimento").val()
        editing.bio = $("#biografia").val()
        editing.address = $("#endereco").val()
        requestPost()
    })

    $(document).on("click", ".editButton", function () {
        tr = $(this).closest("tr")
        data = bodyList[tr.data("id")]
        if (data) {
            editing = data
            $('#modalURLImage').modal('show')
            updateEditModal(editing)
        } else {
            alert("Erro ao achar ID")
        }
    })

    $("#elementfotoPerfil").on("click", function () {
        if (editing) {
            $("#inputLinkImagem").val(editing.imageURL)
            $('#modalLinkImagem').modal('show')
        }
    })

    $("#btnSalvarImagem").on('click', function (event) {
        editing.imageURL = $("#inputLinkImagem").val()
        $("#elementfotoPerfil").attr("src", editing.imageURL);
        $('#modalLinkImagem').modal('hide')
    });

    $('.modal').on('hidden.bs.modal', function () {
        if (this.id == "modalURLImage") {
            requestGET()
        }
    });

})

bodyList = []

lorem = "Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolor atque quae quia esse aliasnatus vitae voluptates odit, reiciendis inventore doloremque nobis eligendi iusto illo estsimilique accusamus recusandae delectus."

function insertData(usuarios) {
    $("#tbodi").empty()
    /*usuarios = [
{id: 1, fullname: 'Nome completo da silva', birthday: '2025-10-20', bio: lorem, address: 'Rua sem nome, número, Cidade, Estado - País', imageURL: 'https://ichef.bbci.co.uk/ace/ws/640/amz/worldservice/live/assets/images/2015/09/26/150926165742__85730600_monkey2.jpg.webp' },
{id: 2, fullname: 'Nome completo da silva', birthday: '2025-10-20', bio: lorem, address: 'Rua sem nome, número, Cidade, Estado - País', imageURL: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSpfOrHWHIKmVFphXBNvL-yLhZy-tCoFlZzcA&s' },
];*/
    usuarios.forEach(element => {
        insertRow(element)
        bodyList[element.id] = element
    });

}

function insertRow(data) {
    const $tbody = $("#tbodi");
    const $row = $('<tr data-id=' + data.id + '></tr>');
    $row.append('<td>' + data.id + '</td>');
    $row.append('<td>' + data.fullname + '</td>');
    $row.append('<td>' + formatDateToBR(data.birthday) + '</td>');
    $row.append('<td class="limit-text" title="' + data.bio + '">' + data.bio + '</td>');
    $row.append('<td>' + data.address + '</td>');
    $row.append('<td class="editButton"><span class="badge bg-success">Editar</span></td>');
    $tbody.append($row);
}

function updateEditModal(data) {
    $("#nomeCompleto").val(data.fullname)
    $("#dataNascimento").val(data.birthday)
    $("#biografia").val(data.bio)
    $("#endereco").val(data.address)
    $("#elementfotoPerfil").attr("src", data.imageURL);
}

function formatDateToBR(date) {
    daSplit = date.split("-")

    if (daSplit) {
        return daSplit[2] + "/" + daSplit[1] + "/" + daSplit[0]
    }

    return date
}

function removeIdFromData(data) {
    dataReturn = {}
    $.each(data, function (index, value) {
        if (index != "id") {
            dataReturn[index] = value
        }
    });
    return dataReturn
}

function requestPost() {
    dataToUpdate = removeIdFromData(editing)
    $.ajax({
        accepts: 'application/json',
        type: "POST",
        dataType: "JSON",
        url: "/usuario/" + editing.id,
        data: JSON.stringify(dataToUpdate),
        statusCode: {
            404: function (jqXHR) {
                //alert(jqXHR.responseJSON.message)
            },
            400: function (jqXHR) {
                //alert(jqXHR.responseJSON.message)
            },
        }
    }).done(function (response) {
        $('#modalURLImage').modal('hide')
        alert("Usuário atualizado com sucesso")
        requestGET()
    }).fail(function (jqXHR, textStatus) {
        alert(jqXHR.responseJSON.message)
    });
}

function requestGET() {
    console.log("chamado")
    $.ajax({
        accepts: 'application/json',
        type: "GET",
        dataType: "JSON",
        url: "/usuario/",
        statusCode: {
            404: function () {
                alert("Página não encontrada")
            },
        }
    }).done(function (response) {
        insertData(response.data)
    }).fail(function (jqXHR, textStatus) {
        console.log(jqXHR)
    });
}
