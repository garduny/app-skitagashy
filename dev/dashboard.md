but please I want the dashboard but with some condition : please please understand my tips to start :  

UI same as front :
Tailwind CSS, 
Support DARK|LIGHT THEMES,

I want some main things advanced in the dashboard example : 
pages and cruds : 
header.php,
footer.php,
sidebar,php,
app.php[home cards],
dashboard.php[charts,public accounting,etc..],
login.php,
forgetpassword.php,
resetpassword.php,
profile.php,
setting.php,
system.php,
roles.php,
permissions.php,
users.php,
************************************** this belong to main dashboard that every project need **************************************
note : create db tables for users[admin] it mean all user keyword i changed to account it mean users,user_quests,user_referrals => replaced to accounts,account_quests,account_referrals from api folder and root files it mean : ( account mean account to register wallet and customer, or client ...  and user mean admins that has role and permissions to 
************************************** after this belong to our project **************************************
pages that is cruds it is all in one no need to api : 
auctions.php,
lotteries.php,
mystery-boxes.php,
products.php,
productdetail.php,
sellers.php,
accounts.php,
accountdetail.php,
orders.php,
orderdetail.php,
note : these pages mean cruds or management of the project ok ? 
************************************************************************************************************************

remember : 
- [api] folder no need any change because already [user] replaced to [account]
- start from structure : user_sessions,users,users_forget[forget password code sent to email aftr that will be deleted],[admins that has role and any role has permission]
- if you create any page wait for my approval ok ? 
- start from db structure[roles,permissions,users,users_session,users_forget] , after that pages - tailwind css , responsive mobile , dark|light themes localstorage , etc....
- without comment and empty lines and take care ["QUERY" => " QUERY "]
- setting,system belong to general any project  , and our project maybe need rules right ?
- please please dashboard no need to api ok ? all cruds INSERT,UPDATE,DELETE from same page ok ? example : if post('add'),if post('delete'),if post('edit')

function post($key)
{
    return isset($_POST[$key]);
}

function get($key)
{
    return isset($_GET[$key]);
}

finally : 
please understand from me thanksssssssssssssssssssssssssssssssssssssss