<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/


require('include/fpdf/pdf.php');
require_once('modules/SalesOrder/SalesOrder.php');
require_once('include/database/PearDatabase.php');

global $adb,$app_strings,$products_per_page;

$sql="select currency_symbol from vtiger_currency_info";
$result = $adb->query($sql);
$currency_symbol = $adb->query_result($result,0,'currency_symbol');

// would you like and end page?  1 for yes 0 for no
$endpage="1";
$products_per_page="6";

$id = $_REQUEST['record'];
//retreiving the vtiger_invoice info
$focus = new SalesOrder();
$focus->retrieve_entity_info($_REQUEST['record'],"SalesOrder");
$account_name = getAccountName($focus->column_fields[account_id]);

// **************** BEGIN POPULATE DATA ********************
// populate data
if($focus->column_fields["quote_id"] != '')
	$quote_name = getQuoteName($focus->column_fields["quote_id"]);
else
	$quote_name = '';
$po_name = $focus->column_fields["purchaseorder"];
$subject = $focus->column_fields["subject"];

$valid_till = $focus->column_fields["duedate"];
$valid_till = getDisplayDate($valid_till);
$bill_street = $focus->column_fields["bill_street"];
$bill_city = $focus->column_fields["bill_city"];
$bill_state = $focus->column_fields["bill_state"];
$bill_code = $focus->column_fields["bill_code"];
$bill_country = $focus->column_fields["bill_country"];

$ship_street = $focus->column_fields["ship_street"];
$ship_city = $focus->column_fields["ship_city"];
$ship_state = $focus->column_fields["ship_state"];
$ship_code = $focus->column_fields["ship_code"];
$ship_country = $focus->column_fields["ship_country"];

$conditions = $focus->column_fields["terms_conditions"];
$description = $focus->column_fields["description"];
$status = $focus->column_fields["sostatus"];

// Company information
$add_query = "select * from vtiger_organizationdetails";
$result = $adb->query($add_query);
$num_rows = $adb->num_rows($result);

if($num_rows == 1)
{
		$org_name = $adb->query_result($result,0,"organizationname");
		$org_address = $adb->query_result($result,0,"address");
		$org_city = $adb->query_result($result,0,"city");
		$org_state = $adb->query_result($result,0,"state");
		$org_country = $adb->query_result($result,0,"country");
		$org_code = $adb->query_result($result,0,"code");
		$org_phone = $adb->query_result($result,0,"phone");
		$org_fax = $adb->query_result($result,0,"fax");
		$org_website = $adb->query_result($result,0,"website");

		$logo_name = $adb->query_result($result,0,"logoname");
}

//Population of Product Details - Starts

//we can cut and paste the following lines in a file and include that file here is enough. For that we have to put a new common file. we will do this later
//NOTE : Removed currency symbols and added with Grand Total text. it is enough to show the currency symbol in one place

//we can also get the NetTotal, Final Discount Amount/Percent, Adjustment and GrandTotal from the array $associated_products[1]['final_details']

//getting the Net Total
$price_subtotal = number_format($focus->column_fields["hdnSubTotal"],2,'.',',');

//Final discount amount/percentage
$discount_amount = $focus->column_fields["hdnDiscountAmount"];
$discount_percent = $focus->column_fields["hdnDiscountPercent"];

if($discount_amount != "")
	$price_discount = number_format($discount_amount,2,'.',',');
else if($discount_percent != "")
	$price_discount = $discount_percent."%";
else
	$price_discount = "0.00";

//Adjustment
$price_adjustment = number_format($focus->column_fields["txtAdjustment"],2,'.',',');
//Grand Total
$price_total = number_format($focus->column_fields["hdnGrandTotal"],2,'.',',');


//get the Associated Products for this Invoice
$focus->id = $focus->column_fields["record_id"];
$associated_products = getAssociatedProducts("SalesOrder",$focus);
$num_products = count($associated_products);

//This $final_details array will contain the final total, discount, Group Tax, S&H charge, S&H taxes and adjustment
$final_details = $associated_products[1]['final_details'];

//To calculate the group tax amount
if($final_details['taxtype'] == 'group')
{
	$group_tax_total = $final_details['tax_totalamount'];
	$price_salestax = number_format($group_tax_total,2,'.',',');

	$group_total_tax_percent = '0.00';
	$group_tax_details = $final_details['taxes'];
	for($i=0;$i<count($group_tax_details);$i++)
	{
		$group_total_tax_percent = $group_total_tax_percent+$group_tax_details[$i]['percentage'];
	}
}

//S&H amount
$sh_amount = $final_details['shipping_handling_charge'];
$price_shipping = number_format($sh_amount,2,'.',',');

//S&H taxes
$sh_tax_details = $final_details['sh_taxes'];
$sh_tax_percent = '0.00';
for($i=0;$i<count($sh_tax_details);$i++)
{
	$sh_tax_percent = $sh_tax_percent + $sh_tax_details[$i]['percentage'];
}
$sh_tax_amount = $final_details['shtax_totalamount'];
$price_shipping_tax = number_format($sh_tax_amount,2,'.',',');


//This is to get all prodcut details as row basis
for($i=1,$j=$i-1;$i<=$num_products;$i++,$j++)
{
	$product_name[$i] = $associated_products[$i]['productName'.$i];
	$prod_description[$i] = $associated_products[$i]['productDescription'.$i];
	$product_id[$i] = $associated_products[$i]['hdnProductId'.$i];
	$qty[$i] = $associated_products[$i]['qty'.$i];
	$unit_price[$i] = number_format($associated_products[$i]['unitPrice'.$i],2,'.',',');
	$list_price[$i] = number_format($associated_products[$i]['listPrice'.$i],2,'.',',');
	$list_pricet[$i] = $associated_products[$i]['listPrice'.$i];
	$discount_total[$i] = $associated_products[$i]['discountTotal'.$i];
	
	$taxable_total = $qty[$i]*$list_pricet[$i]-$discount_total[$i];

	$producttotal = $taxable_total;
	$total_taxes = '0.00';
	if($focus->column_fields["hdnTaxType"] == "individual")
	{
		$total_tax_percent = '0.00';
		//This loop is to get all tax percentage and then calculate the total of all taxes
		for($tax_count=0;$tax_count<count($associated_products[$i]['taxes']);$tax_count++)
		{
			$tax_percent = $associated_products[$i]['taxes'][$tax_count]['percentage'];
			$total_tax_percent = $total_tax_percent+$tax_percent;
			$tax_amount = (($taxable_total*$tax_percent)/100);
			$total_taxes = $total_taxes+$tax_amount;
		}
		$producttotal = $taxable_total+$total_taxes;
		$product_line[$j]["Tax"] = number_format($total_taxes,2,'.',',')."\n ($total_tax_percent %) ";
	}
	$prod_total[$i] = number_format($producttotal,2,'.',',');

	$product_line[$j]["Product Name"] = $product_name[$i];
	$product_line[$j]["Description"] = $prod_description[$i];
	$product_line[$j]["Qty"] = $qty[$i];
	$product_line[$j]["Price"] = $list_price[$i];
	$product_line[$j]["Discount"] = $discount_total[$i];
	$product_line[$j]["Total"] = $prod_total[$i];
}
//echo '<pre>Product Details ==>';print_r($product_line);echo '</pre>';
//echo '<pre>';print_r($associated_products);echo '</pre>';


//Population of Product Details - Ends


// ************************ END POPULATE DATA ***************************8

$page_num='1';
$pdf = new PDF( 'P', 'mm', 'A4' );
$pdf->Open();

$num_pages=ceil(($num_products/$products_per_page));


$current_product=0;
for($l=0;$l<$num_pages;$l++)
{
	$line=array();
	if($num_pages == $page_num)
		$lastpage=1;

	while($current_product != $page_num*$products_per_page)
	{
		$line[]=$product_line[$current_product];
		$current_product++;
	}

	$pdf->AddPage();
	include("pdf_templates/header.php");
	include("include/fpdf/templates/body.php");
	include("pdf_templates/footer.php");

	$page_num++;

	if (($endpage) && ($lastpage))
	{
		$pdf->AddPage();
		include("pdf_templates/header.php");
		include("pdf_templates/lastpage/body.php");
		include("pdf_templates/lastpage/footer.php");
	}
}


$pdf->Output('SalesOrder.pdf','D'); //added file name to make it work in IE, also forces the download giving the user the option to save

exit();
?>
