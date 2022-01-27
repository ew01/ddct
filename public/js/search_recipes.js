async function search_recipes (searchTerm){
    //alert('Your search terms are ' + searchTerm.value);
    //https://dev.ellenburgweb.host/dietdr/wp-json/wp/v2/search/?search=first&type=post&subtype=recipes

    const response = await fetch('https://dev.ellenburgweb.host/dietdr/wp-json/wp/v2/search/?type=post&subtype=recipes&search='+searchTerm.value);

    const recipes = await response.json();

    //alert(obj[0].title);
    let html='';
    let rating= 0;


    for(let recipe of recipes){
        //alert(recipe.title);

        const recipeResponse= await fetch('https://dev.ellenburgweb.host/dietdr/wp-json/wp/v2/recipes/'+recipe.id)

        const recipeDetails = await recipeResponse.json();

        switch (recipeDetails.meta.recipe_rating){
            case 'one':
                rating= 1;
                break;
            case 'two':
                rating= 2;
                break;
            case 'three':
                rating= 3;
                break;
            case 'four':
                rating= 4;
                break;
            case 'five':
                rating= 5;
                break;
        }

        html= html+
            "<div class='recipe_card'>" +
            "<span class='recipe_title'><a href='"+recipe.url+"'>"+recipe.title+"</a></span>" +
            "<span>Rating: "+rating+" out of 5 | Cook Time: "+recipeDetails.meta.recipe_cook_time+"</span>" +
            "<p>"+recipeDetails.meta.recipe_short_description+"</p>" +
            "</div>";
    }

    document.getElementById('searchResults').innerHTML= html;
}




//https://www.w3schools.com/howto/howto_js_autocomplete.asp
//
var searchTerms = ["recipe", "one", "two"];

function autocomplete(inp, arr) {
    /*the autocomplete function takes two arguments,
    the text field element and an array of possible autocompleted values:*/
    var currentFocus;
    /*execute a function when someone writes in the text field:*/
    inp.addEventListener("input", function(e) {
        var a, b, i, val = this.value;
        /*close any already open lists of autocompleted values*/
        closeAllLists();
        if (!val) { return false;}
        currentFocus = -1;
        /*create a DIV element that will contain the items (values):*/
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        /*append the DIV element as a child of the autocomplete container:*/
        this.parentNode.appendChild(a);
        /*for each item in the array...*/
        for (i = 0; i < arr.length; i++) {
            /*check if the item starts with the same letters as the text field value:*/
            if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                /*create a DIV element for each matching element:*/
                b = document.createElement("DIV");
                /*make the matching letters bold:*/
                b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                b.innerHTML += arr[i].substr(val.length);
                /*insert a input field that will hold the current array item's value:*/
                b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                /*execute a function when someone clicks on the item value (DIV element):*/
                b.addEventListener("click", function(e) {
                    /*insert the value for the autocomplete text field:*/
                    inp.value = this.getElementsByTagName("input")[0].value;
                    /*close the list of autocompleted values,
                    (or any other open lists of autocompleted values:*/
                    closeAllLists();
                });
                a.appendChild(b);
            }
        }
    });
    /*execute a function presses a key on the keyboard:*/
    inp.addEventListener("keydown", function(e) {
        var x = document.getElementById(this.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) {
            /*If the arrow DOWN key is pressed,
            increase the currentFocus variable:*/
            currentFocus++;
            /*and and make the current item more visible:*/
            addActive(x);
        } else if (e.keyCode == 38) { //up
            /*If the arrow UP key is pressed,
            decrease the currentFocus variable:*/
            currentFocus--;
            /*and and make the current item more visible:*/
            addActive(x);
        } else if (e.keyCode == 13) {
            /*If the ENTER key is pressed, prevent the form from being submitted,*/
            e.preventDefault();
            if (currentFocus > -1) {
                /*and simulate a click on the "active" item:*/
                if (x) x[currentFocus].click();
            }
        }
    });
    function addActive(x) {
        /*a function to classify an item as "active":*/
        if (!x) return false;
        /*start by removing the "active" class on all items:*/
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        /*add class "autocomplete-active":*/
        x[currentFocus].classList.add("autocomplete-active");
    }
    function removeActive(x) {
        /*a function to remove the "active" class from all autocomplete items:*/
        for (var i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
        }
    }
    function closeAllLists(elmnt) {
        /*close all autocomplete lists in the document,
        except the one passed as an argument:*/
        var x = document.getElementsByClassName("autocomplete-items");
        for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
                x[i].parentNode.removeChild(x[i]);
            }
        }
    }
    /*execute a function when someone clicks in the document:*/
    document.addEventListener("click", function (e) {
        closeAllLists(e.target);
    });
}

window.onload = async function() {
    const recipeList = await fetch('https://dev.ellenburgweb.host/dietdr/wp-json/wp/v2/search/?type=post&subtype=recipes');
    const recipesList = await recipeList.json();

    for(let recipe of recipesList){
        searchTerms.push(recipe.title);
    }

    //alert(searchTerms);
    autocomplete(document.getElementById("recipe_name"), searchTerms);
};

