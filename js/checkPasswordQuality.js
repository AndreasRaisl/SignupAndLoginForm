function checkPasswordQuality(password)
{
  if (password == "")
  {
    document.getElementById("SafetyInfo").innerHTML = "Keine Passworteingabe";
    return;
  }	
  if (window.XMLHttpRequest)
  {
    xmlhttp = new XMLHttpRequest();
  }
  else
  {
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
      document.getElementById("SafetyInfo").innerHTML = xmlhttp.responseText;
    }
  }
  xmlhttp.open("GET", "kennworttesten.php?q="+password, true);
  xmlhttp.send();
}		