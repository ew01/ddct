# Diet Dr Code Test


Usage:
Install plugin to gain access to the recipe post type, and it's associated custom fields.
Use the shortcode [recipe_search_page] to display the custom search page.

## Improvement Ideas:

* Scalability
* * Custom fields should be added by an array that is accessed by the custom field function in  the class, perhaps even a class that handles custom fields.
This would then allow for new custom fields being added more easily, and no need to write the wp meta hook over and over.
* * I would also create each new custom post type as its own class, that class handling the creation, rest api and custom fields for that post type.
This would be a better option than the first one if more than one custom post type are needed, if this plugin only ever adds one custom post type, then the above option will suffice.


## Change Log

### Version 1.0.1

* Added an auto complete to the search page
* * While most of the auto complete comes from W3Schools(https://www.w3schools.com/howto/howto_js_autocomplete.asp) I added a simple function to dynamically inject recipe titles into the search terms array. The goal being that when new recipes are added, they are automatically added to the auto-complete options.
### Version 1.0.0

* This is version one of my Diet Dr Code Test.
* This is about 4 hours of work.
* Custom post type of Recipes.
* Custom Fields for Recipe post type: Rating, Cook Time, Short Description
* Both the custom post type and custom fields are added to the WP rest api.
* Front end recipe search page.
* Simple design for search results, linking to the full recipe.
