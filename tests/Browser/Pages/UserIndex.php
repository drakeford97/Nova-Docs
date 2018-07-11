<?php

namespace Tests\Browser\Pages;

use App\User;
use Laravel\Dusk\Browser;

class UserIndex extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/nova/resources/users';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        // $browser->assertPathIs($this->url());
    }

    /**
     * Wait for the users to be present.
     */
    public function waitForUsers(Browser $browser)
    {
        $browser->waitForText(User::find(1)->name)->pause(500);
    }

    /**
     * Search for the given string.
     */
    public function searchForUser(Browser $browser, $search)
    {
        $browser->type('@users-search', $search)->pause(1000);
    }

    /**
     * Assert on the matching total matching user count text.
     */
    public function assertSelectAllMatchingCount(Browser $browser, $count)
    {
        $browser->click('@users-select-all-menu')
                        ->pause(500)
                        ->assertSee('Select All Matching ('.$count.')')
                        ->click('@users-select-all-menu')
                        ->pause(250);
    }

    /**
     * Check the user at the given resource table row index.
     */
    public function clickCheckboxAtIndex(Browser $browser, $index)
    {
        $browser->click('[dusk="users-items-'.$index.'-checkbox"] div.checkbox')
                        ->pause(50);
    }

    /**
     * Delete the user at the given resource table row index.
     */
    public function deleteUserAtIndex(Browser $browser, $index)
    {
        $browser->click('@users-items-'.$index.'-delete-button')
                        ->pause(250)
                        ->click('@confirm-delete-button')
                        ->pause(500);
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [];
    }
}
