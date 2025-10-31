<?php
/**
 * @file
 * Contains \Drupal\hit_digital_gov_search\src\Controller\AutocompleteController.
 */
namespace Drupal\hit_digital_gov_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Tags;
use Drupal\Component\Utility\Unicode;

/**
 * Defines a route controller for entity autocomplete form elements.
 *
 * Reference: http://www.qed42.com/blog/autocomplete-drupal-8.
 */
class AutocompleteController extends ControllerBase {

  /**
   * Handler for autocomplete request.
   */
  public function handleAutocomplete(Request $request) {
    $results = [];

    // Get the typed string.
    if ($input = $request->query->get('q')) {
      $typed_string = Tags::explode($input);
      $typed_string = mb_strtolower(array_pop($typed_string));
      // Run the keywords through Digital Gov Search to get JSON results.
      $site_name = \Drupal::request()->getHost();
      $searchResults = json_decode(file_get_contents('https://search.usa.gov/sayt?name=www.healthit.gov&q=' . $typed_string));
      foreach ($searchResults as $searchResult) {
        $results[] = ['value' => $searchResult,'label' => $searchResult];
      }
    }

    return new JsonResponse($results);
  }

}
