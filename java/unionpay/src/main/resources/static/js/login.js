
function encryptPadding(data) {
    var key  = CryptoJS.enc.Utf8.parse("1234123412ABCDEF");
    var iv   = CryptoJS.enc.Utf8.parse("1234123412ABCDEF");
    return CryptoJS.AES.encrypt(data, key, {iv:iv,mode:CryptoJS.mode.CBC}).toString();
}




function login(){
    var account =  $("#account").val();
    var inpass = $("#pwd").val();
    var en_username = encryptPadding(account);
    var en_password = encryptPadding(inpass);
    $("#account").val("");
    $("#pwd").val("");
    $("#password").attr("value", en_password);
    $("#username").attr("value", en_username);
    $("#loginForm").submit();





}