<?php
if(isset($_POST['change'])) {
 global $user;
  $username=$_POST['name'];
$Password=$_POST['oldpassword'];
$nPassword=$_POST['newpassword'];
//require_once 'includes/password.inc';
//$u=user_check_password('$Password', '$user->pass');
//$p=user_hash_password('$Password');
 $param1=array('UserName' => $username);
 $client1 = new soapclient('http://etypeservices.com/service_GetPasswordByUserName.asmx?WSDL');
 $resp=$client1->GetPasswordByUserName($param1);
if($Password==$resp->GetPasswordByUserNameResult)
{
 $param=array('UserName'=>$username,'Password' =>$nPassword);
            
                 
                $client = new soapclient('http://etypeservices.com/Service_ChangePassword.asmx?WSDL');
                
         $response=$client->ChangePassword($param);
         //  echo "<pre>";
           //   print_r($response);
           //  echo "</pre>";
             $query="select name, uid from users where name='".$username."'";
        $qu=db_query($query);
        $userexit = "";
        foreach($qu as $qu)
            {
            $userexit = $qu->name;
           
            }
            if($userexit != '')
            {
              $edit['pass'] = $Password;
              user_save($user, $edit);
              $msg="Password Change Successful";
    //drupal_goto('custom-login-page');
            }
            
          }
          else
            {
             $msg="Enter The Correct Old Password";
            }
}
?>

        <?php


               
                  
?>


     <p style="color:red"><?php echo $msg; ?></p>
       <!-- <h4  style="background:gray;line-height: 2.0em;font-size: 16px;"><center>My Account</center></h4>-->
      
     
            <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
                          <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
                          <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
                          
                          <script>
                          $(function() {
                            $( "#tabs" ).tabs();
                          });
                          </script>
                        </head>
                        <body>
                         
                        <div id="tabs">
                          <ul>
                          
                            <li><a href="#tabs-2">Change Password</a></li>
                            
                          </ul>
                         
                          <div id="tabs-2">
                            <p> <div class="login">
      
      <form name="change password" method="POST" action="">
        <p style="color:red"><?php echo $msg; ?></p>
        <table>
          <tr>
            <td><strong>UserName</strong>
            </td>
            <td><label><?php echo $user->name;?></label>
		<input type="hidden" name="name" value="<?php echo $user->name;?>">
            </td></tr>
            <td><strong>Old Password</strong>
            </td>
            <td><input type="password" id="oldpassword" required name="oldpassword" pattern=".{6,}" placeholder="Old Password">
            </td></tr>
            <td><strong>New Password</strong>
            </td>
            <td><input type="password" id="newpassword" required name="newpassword" pattern=".{6,}" placeholder="Min. 6 character">
            </td></tr>
            <tr>
            <td><strong>Confirm Password</strong>
            </td>
            <td><input type="password" id="confirmpassword" required name="confirmpassword" pattern=".{6,}" oninput="check(this)" placeholder="Confirm Password">
            </td>
          </tr>
        </table>  
        
        
        <p class="submit">
        <input type="submit" name="change" value="Submit" style="background-color: gainsboro;border-radius: 4px;width: 93px;height: 28px;border: 1px solid #CCC;text-decoration: none;color: #000;text-shadow: white 0 1px 1px;padding: 2px;" />
        </p>
      </form>
      <script language='javascript' type='text/javascript'>
function check(input) {
    if (input.value != document.getElementById('newpassword').value) {
        input.setCustomValidity('The two passwords must match.');
    } else {
        // input is valid -- reset the error message
        input.setCustomValidity('');
   }
}

</script>   
    </div></p>
                          </div>
                          
                        
                         

        
 
 
           
            
          
                        </div>
        

   