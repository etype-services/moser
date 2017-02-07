

<?php
/*
if(isset($_POST['submit'])) {
      $email=$_POST['email'];
      $UserName=$_POST['name'];
   $_SESSION['username']=$UserName;
      $Password=$_POST['password'];
      $firstname=$_POST['firstname'];
      $lastname=$_POST['lastname'];
      $address=$_POST['address'];
      $city=$_POST['city'];
      $state=$_POST['state'];
      $postal=$_POST['postalcode'];
      $phone=$_POST['phone'];
      $publicationid=$_POST['Publication_Id'];
      $planid=$_POST['planId'];
      $date = date("Y-m-d");// current date
      
      $expdate = date('Y-m-d', strtotime("+".$_POST['Period']." ".$_POST['PeriodType'].""));
      
      $param=array('Email' =>"$email",'UserName' =>"$UserName",'Password' =>"$Password",'FirstName' =>"$firstname",'LastName'=>"$lastname",'StreetAddress'=>"$address",'City'=>"$city",'State'=>"$state",'PostalCode'=>"$postal",'Phone'=>"$phone",'ExpiryDate'=>"$expdate",'PublicationID'=>"$publicationid",'SubscriptionPlanID'=>"$planid");
    }*/
           // echo "<pre>";
            //  print_r($param);
             // echo "</pre>";
  
         $publicationid=$_SESSION['Publication_Id'];
        $client1= new soapclient('http://etypeservices.com/Service_GetPublisherPaypalEmailID.asmx?WSDL');
  $param1=array('PublicationID'=>"$publicationid");
  $response1=$client1->GetPublisherPaypalEmailID($param1);
         // echo "<pre>";
           //   print_r($response1);
            // echo "</pre>";
             ?>

             <div style="margin-left:30px">

<form name="form2" method="POST" action="http://www.vcnewsreview.com/confirm-payment" enctype="multipart/form-data">

                <h3 style="Background:gray;line-height: 2.0em;font-size:16px"><center>REVIEW ORDER</center></h3>
                <div style="">
                    <p>
                        Your order is almost complete. Please review the details below and click 'Continue' if all the information is correct. You may use the 'Back' button to make changes to your order if necessary.
                    </p>
                    <h4 style="Background:gray;line-height: 2.0em;font-size:14px"><center>Cart Content</center></h4>
                    <table style="width: 100%;">
                        
                        <tr>
                            <td>Quantity
                            </td>
                              <td>Product
                            </td>
                              <td>Price
                            </td>
                            </tr>
                                 <tr>
                            <td>1*
                            </td>
                              <td><?php echo $_SESSION['planTitle'] ;echo $_SESSION['Period'] ;echo $_SESSION['PeriodType'] ;?>
                            </td>
                              <td>$<?php echo $_SESSION['planPrice'] ?>
                            </td>
                            </tr>
                    </table>
                    <h4 style="Background:gray;line-height: 2.0em;font-size:14px"><center>Customer information</center></h4>
                    <table  style="width: 100%;">
                        
                        <tr>
                            <td>Email
                            
                            </td>
                            
                              <td><?php echo $_SESSION['email'] ;?>
                            </td>
                             
                            </tr>
                    </table>
                     <h4 style="Background:gray;line-height: 2.0em;font-size:14px"><center>Billing information</center></h4>
                    <table  style="width: 100%;">
                        
                        <tr>
                            <td>Address
                            
                          </td>
                            
                              <td><?php echo $_SESSION['firstname'] ;echo "&nbsp";echo $_SESSION['lastname'] ;
                                    echo "<br/>" ;
                                    echo $_SESSION['address']; echo "<br/>" ;
                                    echo $_SESSION['city']; echo "<br/>" ;
                                    echo $_SESSION['state'];echo "&nbsp";
                                    echo $_SESSION['postalcode'];

                              ?>
                            </td>
                             
                            </tr>
                    </table>
                    <!--
                    <h4 style="Background:gray;line-height: 2.0em;font-size:14px"><center>Payment Method</center></h4>
                    <table  style="width: 100%;">
                        
                        <tr>
                            <td>Subtotal
                            </td>
                            
                                <td><?php echo $_SESSION['planPrice'] ;
                                                                  ?>
                            </td>
                             </tr>
                            
                            <tr>
                             <td>Order Total
                            </td>
                            <td><?php echo $_SESSION['planPrice'] 
                                                                  ?>
                            </td>

                        </tr>
                        <tr>
                             <td>Paying By
                            </td>
                            
                              <td><?php echo "PayPal";
                                                                  ?>
                            </td>
                            </tr>
                    </table>-->
<br/>
<div>
     <input type="submit" name="submit" value="Continue" style="float:left;background-color: gainsboro;border-radius: 4px;width: 93px;height: 28px;border: 1px solid #CCC;text-decoration: none;color: #000;text-shadow: white 0 1px 1px;padding: 2px;">

                    <a href="javascript:history.back()" style="text-decoration: none;color: #000;font-weight: bold; float:right;">
            <div style="width: 93px;float: right;background-color: gainsboro;height: 25px;border: 1px solid #ccc;border-radius: 4px;text-shadow: white 0 1px 1px;"><p style="margin-left: 30px;margin-top: 4px;">Back</p></div></a>
<br/>
               <!-- <input type="submit" name="submit" value="Submit Order!" />--></div>
     
                </div>
</form>
             </div>

      