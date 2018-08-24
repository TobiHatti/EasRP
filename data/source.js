function AutoFillProducer()
{
    var listpre = document.getElementById("producer_list");
    var selected = listpre.options[listpre.selectedIndex].value;

    if(selected != "none")
    {
        document.getElementById("producer").value = selected;
        document.getElementById("producer_list").selectedIndex = "0";
    }
}

function AutoFillRetailer()
{
    var listpre = document.getElementById("retailer_list");
    var selected = listpre.options[listpre.selectedIndex].value;

    if(selected != "none")
    {
        document.getElementById("retailer").value = selected;
        document.getElementById("retailer_list").selectedIndex = "0";
    }
}

function ProductSelectEdit()
{
    var listpre = document.getElementById("product_list");
    var selected = listpre.options[listpre.selectedIndex].value;

    if(selected != "none")
    {
        var newLink = document.getElementById("extension").value + "=" + selected;
        document.getElementById("loader").style.display="table-row";
        window.location.replace(newLink);
    }
}

function ProductSelectAddP()
{
    var listpre = document.getElementById("product_list");
    var selected = listpre.options[listpre.selectedIndex].value;

    if(selected != "none")
    {
        var newLink = document.getElementById("extension").value + "&selP=" + selected;
        document.getElementById("loader").style.display="table-row";
        window.location.replace(newLink);
    }
}

function UpdateUnit()
{
    var listpre = document.getElementById("unit_list");
    var selected = listpre.options[listpre.selectedIndex].value;

    document.getElementById("unit").value = selected;
}

function UpdateSellableText()
{
    if(document.getElementById("check_sellable").checked) document.getElementById("out_sellable").value = "Zum Verkauf freigegeben";
    else document.getElementById("out_sellable").value = "Nicht zum Verkauf freigegeben";
}

function CheckMail()
{
    var email = document.getElementById("customer_email").value;
    var re = /\S+@\S+\.\S+/;
    var mail_ok = false;

    mail_ok = re.test(email);

    if(mail_ok == true || document.getElementById("customer_email").value == "") document.getElementById("email_out").value = " ";
    else document.getElementById("email_out").value = "Geben Sie eine g\u00fcltige E-Mail Adresse ein!";

    if(mail_ok == true) document.getElementById("continue_order").disabled = false;
    else document.getElementById("continue_order").disabled = true;
}

function CheckCustomerNumber()
{
    var cnr = document.getElementById("customer_number").value;
    var prefix = document.getElementById("prefix_customer").value;
    var re = prefix + "-";
    var cnr_ok = false;

    cnr_ok = (new RegExp(re)).test(cnr);

    if(cnr_ok == true || document.getElementById("customer_number").value == "") document.getElementById("cnr_out").value = " ";
    else document.getElementById("cnr_out").value = "Kundennummer muss mit \"" + prefix + "-\" beginnen";
}

function UpdateFlag()
{
    var listpre = document.getElementById("country_list");
    var selected = listpre.options[listpre.selectedIndex].value;

    document.getElementById("flag_img").className = "flag flag-" + selected.toLowerCase();
}

function DisplayOrderConfirmationMessage()
{
    if(document.getElementById("sendOrderConfirmation").checked) document.getElementById("outSendOrderMessage").value="Auftragsbest\u00e4tigung versenden";
    else document.getElementById("outSendOrderMessage").value="Keine Auftragsbest\u00e4tigung versenden";
}

function DisplayShippingCostsMessage()
{
    if(document.getElementById("addShippingCosts").checked) document.getElementById("outShippingCostsMessage").value="Versandkosten Anrechen (+?,00 \u20AC)";
    else document.getElementById("outShippingCostsMessage").value="Keine Versandkosten anrechnen";

    var frameUrl = document.getElementById("orderConfirmationFrame").src;
    if(document.getElementById("addShippingCosts").checked) document.getElementById("orderConfirmationFrame").src = frameUrl.replace("ship=0","ship=1");
    else document.getElementById("orderConfirmationFrame").src = frameUrl.replace("ship=1","ship=0");
}

function PaymentConfirmation()
{
    var listpre = document.getElementById("paymentType");
    var selected = listpre.options[listpre.selectedIndex].value;
    var shipment_checked;
    var order_number = document.getElementById("orderNumber").value;

    if(document.getElementById("addShippingCosts").checked) shipment_checked = 1;
    else shipment_checked = 0;

    document.getElementById("orderConfirmationFrame").src = "/pdfOrderConfirmation?orderNumber=" + order_number + "&ship=" + shipment_checked + "&payment=" + selected;

    if(document.getElementById("paymentType").value != "none" && document.getElementById("paymentStatus").value != "none")  document.getElementById("finishOrderConfirmed").disabled = false;
    else document.getElementById("finishOrderConfirmed").disabled = true;
}

function SetCurrentListItem(index,title)
{
    sessionStorage.setItem("selectedIndex",index);
    sessionStorage.setItem("selectedIndexName",title);
}

function StockAmtCheck()
{
    var max = document.getElementById("max_stock").value;
    var reorder = document.getElementById("reorder_stock").value;
    var security = document.getElementById("security_stock").value;

    if(security <= reorder && reorder <= max)
    {
        document.getElementById("out_stockdata_line1").value = "";
        document.getElementById("out_stockdata_line2").value = "";
        document.getElementById("continue_btn").disabled = false;
    }
    else
    {
        document.getElementById("out_stockdata_line1").value = "Bedingung nicht erf\u00fcllt:";
        document.getElementById("out_stockdata_line2").value = "H\u00f6chstbestand > Meldebestand > Sicherheitsbestand";
        document.getElementById("continue_btn").disabled = true;
    }
}

function UpdateNewStockDisplay()
{
    if(document.getElementById("stock_add").value != 0) document.getElementById("out_newStock").value = parseInt(document.getElementById("stock_current").value) + parseInt(document.getElementById("stock_add").value);
    else document.getElementById("out_newStock").value = parseInt(document.getElementById("stock_current").value);
}

function ProductSelectRawReorder()
{
    var listpre = document.getElementById("product_list");
    var selected = listpre.options[listpre.selectedIndex].value;

    if(selected != "none")
    {
        var newLink = document.getElementById("extension").value + "=" + selected;
        document.getElementById("loader").style.display="table-row";
        window.location.replace(newLink);
    }
}

function UpdateDeclarationPreview1()
{
    if(!document.getElementById("hasSubGroups").checked) document.getElementById("out_subgroup2").value="";
    else document.getElementById("out_subgroup2").value="??";


    document.getElementById("out_subgroup1").value=document.getElementById("subGroup1Short").value;

}

function UpdateDeclarationPreview2()
{
    var listpre = document.getElementById("subGroup1Select");
    var selected = listpre.options[listpre.selectedIndex].value;

    if(document.getElementById("subGroup2Short").value != "") document.getElementById("out_subgroup2").value=document.getElementById("subGroup2Short").value;
    else document.getElementById("out_subgroup2").value="??";
    document.getElementById("out_subgroup1").value=selected;
}

function PopulateSubGroup2()
{
    var listpre = document.getElementById("subGroup1");
    var selected = listpre.options[listpre.selectedIndex].value;
    var select = document.getElementById("subGroup2");

    var select_p = selected.split('|-|');
    var items_p;
    var group1Value = select_p[0];

    if(select_p[1]!=0)
    {
        document.getElementById("subGroup2").style.display = "block";
        select.options[select.options.length] = new Option("Untergruppe-2", "");
        for(var i=2; i<select_p.length-1;i++)
        {
            items_p = select_p[i].split('||');
            select.options[select.options.length] = new Option(items_p[1], items_p[0]);
        }
    }
    else document.getElementById("subGroup2").style.display = "hidden";
    document.getElementById("product_number").value = document.getElementById("product_prefix").value + "-" + group1Value;
    document.getElementById("group1Value").value=group1Value;
}

function UpdateSubGroup2()
{
    var listpre = document.getElementById("subGroup2");
    var selected = listpre.options[listpre.selectedIndex].value;
    var group1Value = document.getElementById("group1Value").value;

    document.getElementById("product_number").value = document.getElementById("product_prefix").value + "-" + group1Value + selected;
    document.getElementById("group2Value").value = selected;
}

function UpdateSubGroupNumber()
{
    var group2Value = document.getElementById("group2Value").value;
    var group1Value = document.getElementById("group1Value").value;
    var prefix = document.getElementById("product_prefix").value;

    document.getElementById("product_number").value = prefix + "-" + group1Value + group2Value + document.getElementById("runnumber").value;
}

function EnableDeclarationAssistent()
{
    if(document.getElementById("enableDecAssist").checked)
    {
        document.getElementById("declarationAssist_disabled").style.display = "none";
        document.getElementById("declarationAssist_enabled").style.display = "block";
    }
    else
    {
        document.getElementById("declarationAssist_disabled").style.display = "block";
        document.getElementById("declarationAssist_enabled").style.display = "none";
    }
}