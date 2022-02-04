<?php

namespace Laravel\Nova\Tests\Browser;

use App\Models\Captain;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Browser;
use Laravel\Nova\Testing\Browser\Pages\Create;
use Laravel\Nova\Testing\Browser\Pages\Detail;
use Laravel\Nova\Testing\Browser\Pages\Update;
use Laravel\Nova\Tests\DuskTestCase;

class FileAttachTest extends DuskTestCase
{
    /**
     * @test
     */
    public function file_can_be_attached_to_resource()
    {
        $this->artisan('storage:link');

        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                    ->visit(new Create('captains'))
                    ->type('@name', 'Taylor Otwell')
                    ->attach('@photo', __DIR__.'/Fixtures/StardewTaylor.png')
                    ->create();

            // Verify the photo in the information in the database...
            $captain = Captain::orderBy('id', 'desc')->first();
            $this->assertNotNull($photo = $captain->photo);
            $this->assertTrue(File::exists(storage_path("app/public/{$photo}")));
            Storage::disk('public')->assertExists($photo);

            // Download the file...
            $browser->on(new Detail('captains', $captain->id))
                    ->waitFor('@photo-download-link')
                    ->click('@photo-download-link')
                    ->pause(250);

            // Ensure file is not removed on blank update...
            $browser->visit(new Update('captains', $captain->id))
                    ->update();

            $captain = $captain->fresh();
            $this->assertNotNull($captain->photo);
            $this->assertTrue(File::exists(storage_path("app/public/{$photo}")));
            Storage::disk('public')->assertExists($photo);

            // Delete the file...
            $browser->visit(new Update('captains', $captain->id))
                    ->whenAvailable('@photo-delete-link', function ($browser) {
                        $browser->click('');
                    })
                    ->whenAvailable('.modal[data-modal-open="true"]', function ($browser) {
                        $browser->click('@confirm-upload-delete-button')->pause(250);
                    })
                    ->waitForText('The file was deleted!');

            $browser->blank();

            // Cleanup temporary files.
            File::delete(__DIR__.'/../../'.$photo);

            // Validate file no longer exists.
            $captain = $captain->fresh();
            $this->assertNull($captain->photo);
            $this->assertFalse(File::exists(storage_path("app/public/{$photo}")));
            Storage::disk('public')->assertMissing($photo);
        });
    }
}
