$(".img-thumbnail").on("click", function() {
    $('#preview').attr('src', $(this).attr('src'));
    $('#imagemodal').modal('show');
 });

 function search(str) {
    if (str.length==0) {
            $('.no-search').removeClass("hide");
            $('.livesearch').addClass("hide");
        return;
    }
    $('.no-search').addClass("hide");
    $('.livesearch').removeClass("hide");

    xmlhttp=new XMLHttpRequest();

    xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
        document.getElementById('livesearch-id').innerHTML = this.responseText;
    }
    }
    xmlhttp.open("GET","blog_search.php?search="+str, true);
    xmlhttp.send();
 }

 function validateForm() {
    var area = document.getElementById('description');
    var alert = document.getElementById('alert');
    var minLength = 50;
    if(area.value.length > minLength){
       alert.innerHTML = "Vielen Danke für Ihre ausführliche Beschreibung!";
       return true;
    }
    if(area.value.length <= minLength) {
       alert.innerHTML = (minLength-area.value.length) + " Zeichen fehlen";
       return false;
    }
    return true;
}

function validatePassword() {
    var password = document.getElementById("password");
    var alert = document.getElementById("alert");
    var letter = document.getElementById("letter");
    var capital = document.getElementById("capital");
    var number = document.getElementById("number");
    var length = document.getElementById("length");
    alert.classList.remove("hide");

    var lowerCaseLetters = /[a-z]/g;
    if(password.value.match(lowerCaseLetters)) { 
       letter.innerHTML = "";
    } else {
       letter.innerHTML = "1 Kleinbuchstaben";
    }
    var upperCaseLetters = /[A-Z]/g;
    if(password.value.match(upperCaseLetters)) { 
       capital.innerHTML = "";
    } else {
       capital.innerHTML = "1 Großbuchstaben"
    }

  var numbers = /[0-9]/g;
    if(password.value.match(numbers)) { 
       number.innerHTML = "";
    } else {
       number.innerHTML = "1 Nummer"
    }

    if(password.value.length >= 8) {
      length.innerHTML = ""
    } else {
      length.innerHTML = "mind. 8 Zeichen"
    }
  }
 
//  function togglePost(keyword) {
//     $(".card").each(function(){
//         var keywords = $(this).find('.keywords').html();
//         keywords = keywords.toLowerCase();
       
//         checkForOtherFilters(this);
//         if(!keywords.includes(keyword)) {
//             $(this).parent().removeClass("hide");
//         } 
//         if(!checkForOtherFilters(this)) {
//             $(this).parent().addClass("hide");
//         }

//     });

//  }



// function checkForOtherFilters(element) {
//    var keywordArray = [];
//    var forAttr;
//    $(".filter-list").each(function() {
//       if($(this).hasClass("line-through")) {
//          forAttr = $(this).attr("for");
//          keywordArray.push(forAttr);
//       }
//    })

//    console.log(keywordArray);

//    var keywords = $(element).find('.keywords').html();
//    var output = false;
//    console.log(keywords);
//    keywordArray.forEach(function(item, index, array) {
//       console.log("Check "+keywords+" with "+item);
//       if(keywords.toLowerCase().includes(item.toLowerCase())) {
//          output = true;
//      } 
//     });

//     if(output) {
//        return true;
//     } else {
//        return false;
//     }
   
// }

function togglePost() {
   //First get all active keywords
   var activeKeywords = [];
   $(".filter-list").each(function() {
      if($(this).hasClass("red-text")) {
         var forAttr = $(this).attr("for");
         forAttr = forAttr.toLowerCase();
         activeKeywords.push(forAttr);
      }
   })

   
   
   $(".card").each(function(){
      var postTohide = false;
      var keywordsOfCard = $(this).find("p.keywords").html().toLowerCase();
      activeKeywords.forEach(function(keyword) {
         console.log(keywordsOfCard);
         if(!keywordsOfCard.includes(keyword)){
            postTohide = true;
         }
       });
       //hide, if one of the active keywords are not in the post
       if(postTohide) {
         $(this).parent().addClass("hide");
       } else { //show again if first not true
         $(this).parent().removeClass("hide");
       }
   });
}


 $(".filter-list").on("click", function() {
     
     var forAttr = $(this).attr("for");

     if(forAttr == "badesee") {
        $('#badesee').next().toggleClass("red-text");
        if(document.getElementById('badesee').checked) {
           $('#badesee').attr("checked", false);
           togglePost();
        } else {
           $('#badesee').attr("checked", true);
           togglePost();
        }
    }

     if(forAttr == "angelsee") {
         $('#angelsee').next().toggleClass("red-text");
         if(document.getElementById('angelsee').checked) {
            $('#angelsee').attr("checked", false);
            togglePost();
         } else {
            $('#angelsee').attr("checked", true);
            togglePost();
         }
     }

     if(forAttr == "hundestrand") {
        $('#hundestrand').next().toggleClass("red-text");
        if(document.getElementById('hundestrand').checked) {
           $('#hundestrand').attr("checked", false);
           togglePost();
        } else {
           $('#hundestrand').attr("checked", true);
           togglePost();
        }
    }


    if(forAttr == "wc") {
        $('#wc').next().toggleClass("red-text");
        if(document.getElementById('wc').checked) {
           $('#wc').attr("checked", false);
           togglePost();
        } else {
           $('#wc').attr("checked", true);
           togglePost();
        }
    }


    if(forAttr == "grillen") {
        $('#grillen').next().toggleClass("red-text");
        if(document.getElementById('grillen').checked) {
           $('#grillen').attr("checked", false);
           togglePost();
        } else {
           $('#grillen').attr("checked", true);
           togglePost();
        }
    }

    if(forAttr == "wlan") {
        $('#wlan').next().toggleClass("red-text");
        if(document.getElementById('wlan').checked) {
           $('#wlan').attr("checked", false);
           togglePost();
        } else {
           $('#wlan').attr("checked", true);
           togglePost();
        }
    }

 });

 
$(".close-button").on("click", function() {
   $(this).prev().children().first().toggleClass("image-to-delete");
   var srcOfImage = $(this).prev().children().first().attr("src");


   if($(this).prev().children().first().hasClass("image-to-delete")) {
      $(this).prev().children().first().css("border-color", "red");
      var input = document.createElement("input");
      input.setAttribute("type", "hidden");
      input.setAttribute("name", "imagesToDelete[]");
      input.setAttribute("value", srcOfImage);
      document.getElementById("editForm").appendChild(input);

   } else {
      $(this).prev().children().first().css("border-color", "#dee2e6");
      
      $("input").each(function(){
         if($(this).val() == srcOfImage) {
            $(this).remove();
         }
 
     });
      
   }

   
   
})

