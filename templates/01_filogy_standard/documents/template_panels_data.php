<?php
//Set $template_panels_data that define the customizer field of this template


$template_panels_data = array(

	'widgets' => array(					//this contains the specific widgets
		array( 							
			'class' => 'FILO_Widget_Invbld_Doc_Title',
			'widget_code' => 'FILO_Widget_Invbld_Doc_Title',
		),
		array( 							
			'class' => 'FILO_Widget_Invbld_Logo',
			'widget_code' => 'FILO_Widget_Invbld_Logo',
		),
		array( 							
			'class' => 'FILO_Widget_Invbld_Seller_Address',
			'widget_code' => 'FILO_Widget_Invbld_Seller_Address',
		),
		array( 							
			'class' => 'FILO_Widget_Invbld_Head_Data_Vertical',
			'widget_code' => 'FILO_Widget_Invbld_Head_Data_Vertical',
		),
		
			array( 							
			'class' => 'FILO_Widget_Invbld_Billing_Address',
			'widget_code' => 'FILO_Widget_Invbld_Billing_Address',
		),
		array( 							
			'class' => 'FILO_Widget_Invbld_Shipping_Address',
			'widget_code' => 'FILO_Widget_Invbld_Shipping_Address',
		),
		array( 							
			'class' => 'FILO_Widget_Invbld_Line_Item_Name',
			'widget_code' => 'FILO_Widget_Invbld_Line_Item_Name',
		),
		array( 							
			'class' => 'FILO_Widget_Invbld_Line_Qty',
			'widget_code' => 'FILO_Widget_Invbld_Line_Qty',
		),
		array( 							
			'class' => 'FILO_Widget_Invbld_Line_Unit_Total_Net',
			'widget_code' => 'FILO_Widget_Invbld_Line_Unit_Total_Net',
		),
		array( 							
			'class' => 'FILO_Widget_Invbld_Line_Total_Net',
			'widget_code' => 'FILO_Widget_Invbld_Line_Total_Net',
		),
		array( 							
			'class' => 'FILO_Widget_Invbld_Line_Tax_Labels',
			'widget_code' => 'FILO_Widget_Invbld_Line_Tax_Labels',
		),
		array( 							
			'class' => 'FILO_Widget_Invbld_Line_Total_Tax',
			'widget_code' => 'FILO_Widget_Invbld_Line_Total_Tax',
		),
		array( 							
			'class' => 'FILO_Widget_Invbld_Line_Total_Gross',
			'widget_code' => 'FILO_Widget_Invbld_Line_Total_Gross',
		),
		
		array( 							
			'class' => 'FILO_Widget_Invbld_Notes',
			'widget_code' => 'FILO_Widget_Invbld_Notes',
		),
		array( 							
			'class' => 'FILO_Widget_Invbld_Payment_Data',
			'widget_code' => 'FILO_Widget_Invbld_Payment_Data',
		),
	),
		
	'grids' => array(					//this contains the specific rows
		array(
			'id' => 'Filo_Head_Row_1',
		),
		array(
			'id' => 'Filo_Head_Row_2',
		),
		array(
			'id' => 'Filo_Customer_Addresses_Row',
		),
		/*array(
			'id' => 'Filo_Item_Table_Header',
		),
		array(
			'id' => 'Filo_Item_Table_Body',
		),
		array(
			'id' => 'Filo_Item_Table_Footer',
		),*/
		array(
			'id' => 'Filo_Notes_Row',
		),
		array(
			'id' => 'Filo_Payment_Data_Row',
		),
		
	),
);
/*
(
                    [style] => Array
                        (
                            [id] => my-very-first-new-row
                        )

                )
		

    'widgets' => array(
    	array(
    		'panels_info' => array(
    			'class' => '',
    			'style' => array(
    				'widget_code => '',
    			)
    		)
    	)
    )

    [widgets] => Array
        (
            [0] => Array
                (
                    [panels_info] => Array
                        (
                            [class] => SiteOrigin_Widget_Image_Widget
                            [style] => Array
                                (
                                    [widget_code] => SiteOrigin_Widget_Image_Widget
                                )

                        )

                )

            [1] => Array
                (
                    [panels_info] => Array
                        (
                            [class] => FILO_Widget_Invbld_Seller_Address
                            [style] => Array
                                (
                                    [widget_code] => FILO_Widget_Invbld_Seller_Address
                                )

                        )

                )

            [2] => ....

        )

    [grids] => Array
        (
            [0] => Array
                (
                    [style] => Array
                        (
                            [id] => my-very-first-new-row
                        )

                )

            [1] => Array
                (
                    [style] => Array
                        (
                            [id] => my-first-doc-row
                        )

                )

            [2] => Array
                (
                    [style] => Array
                        (
                            [id] => abc-rowid
                        )

                )


        )



);
*/