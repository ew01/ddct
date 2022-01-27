async function search_recipes (searchTerm){
    //alert('Your search terms are ' + searchTerm.value);
    //https://dev.ellenburgweb.host/dietdr/wp-json/wp/v2/search/?search=first&type=post&subtype=recipes

    const response = await fetch('https://dev.ellenburgweb.host/dietdr/wp-json/wp/v2/search/?type=post&subtype=recipes&search='+searchTerm.value)

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
