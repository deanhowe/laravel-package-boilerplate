<?php

declare(strict_types=1);

namespace Workbench\App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Str;

final class PackageNameCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:rename
                           {package_name : The new name (slug) of the package}
                           {package_vendor : The vendor name (slug) of the package}
                           {package_classname : The new classname of the package}
                           {package_email : The email of the package author}
                           {--current_vendor= : The current (previously updated) vendor name of the package
                           {--current_name= : The current (previously updated) name of the package}
                           {--current_classname= : The current (previously updated) classname of the package}
                           {--current_email : The current (previously updated) email of the package author}';

    /**
     * The description of the command
     *
     * @var string The description of the command
     */
    protected $description = "Renames the package - Should probably run only once after installation, unfortunately it does not self-destruct.";

    /**
     * The composer data from the composer.json file
     *
     * @var array $composerData The composer data from the composer.json file
     */
    private array $composerData;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        /** @psalm-suppress PossiblyInvalidArgument, PossiblyInvalidCast */
        $package_vendor = Str::slug($this->argument('package_vendor'));
        /** @psalm-suppress PossiblyInvalidArgument, PossiblyInvalidCast */
        $package_name = Str::slug($this->argument('package_name'));
        /** @psalm-suppress PossiblyInvalidArgument, PossiblyInvalidCast */
        $package_classname = Str::title($this->argument('package_classname'));

        $package_full_name = Str::lower($package_vendor . '/' . $package_name);

        /** @psalm-suppress PossiblyInvalidArgument, PossiblyInvalidCast */
        $package_email = Str::lower($this->argument('package_email'));

        $composerPath = __DIR__ . '/../../../../composer.json';

        if ($composerFile = file_get_contents($composerPath)) {
            $composerData = (array)json_decode($composerFile, true);

            $this->composerData = $composerData;

            $this->warn("  We are about to rename the package… {$package_full_name} by {$package_email}.");
            $this->newLine();
            $this->warn("        from: deanhowe/laravel-package-boilerplate by deanhowe@gmail.com");
            $this->warn("        to: {$package_full_name} by {$package_email}");
            $this->newLine();
            $this->warn("  We will have more questions for you…");

            if ( ! $this->confirm('Do you wish to continue?', true)) {
                $this->info('Aborting...');
                $this->newLine();
                return;
            }

            $this->composerData['name'] = $package_full_name;

            /** @psalm-suppress MixedArrayAssignment */
            $this->composerData['autoload']['psr-4'] = ["{$package_classname}\\" => "src/"];

            /** @psalm-suppress MixedArrayAssignment */
            $this->composerData['extra']['laravel']['providers'][0] = "{$package_classname}\\Providers\\ServiceProvider";

            /** @psalm-suppress MixedArrayAssignment */
            $this->composerData['description'] = $this->ask('Please enter a description for the package');

            /** @psalm-suppress MixedArrayAssignment */
            $this->composerData['version'] = $this->ask('Please enter a version number for the package');

            /** @psalm-suppress MixedArrayAssignment */
            $this->composerData['type'] = $this->choice(
                'Please enter a type for the package',
                ['library', 'project', 'metapackage', 'composer-plugin'],
                'library'
            );

            /** @psalm-suppress MixedAssignment */
            $keywords = $this->ask('Please enter keywords for the package (comma seperated)');
            if (filled($keywords) && is_string($keywords)) {
                /** @psalm-suppress MixedArrayAssignment */
                $this->composerData['keywords'] = explode(',', $keywords);
            }

            /** @psalm-suppress MixedAssignment */
            $homepage = $this->ask('Please enter a homepage for the package');
            if (filled($homepage)) {
                $this->composerData['homepage'] = $homepage;
            }

            /** @psalm-suppress MixedAssignment */
            $read_me = $this->ask('Where is the README for the package?', 'README.md');
            if (filled($read_me)) {
                $this->composerData['readme'] = $read_me;
            }

            $this->composerData['time'] = Carbon::now()->rawFormat('Y-m-d H:i:s');

            /** @psalm-suppress MixedArrayAssignment */
            if (is_array($composerData['license-options'])) {
                $this->composerData['license'] = $this->choice(
                    'Which license do you want to use?',
                    $composerData['license-options'],
                    8
                );
            }

            $this->composerData['authors'] = [];
            $this->_askForAuthorDetails();

            while ($this->confirm('Add another author?')) {
                $this->_askForAuthorDetails();
            }

            if ($this->confirm('Add details for support?')) {
                if ($this->confirm('Use github values?')) {

                    /** @psalm-suppress MixedArrayAssignment, MixedArgument */
                    $this->composerData['support'] = collect($this->composerData['support'])->map(
                        fn (string $value) => Str::of($value)->replace('deanhowe/laravel-package-boilerplate', $package_full_name)
                    )->toArray();

                    /** @psalm-suppress MixedAssignment */
                    $support_email = $this->ask('Please enter the support email');
                    if (filled($support_email)) {
                        /** @psalm-suppress MixedArrayAssignment */
                        $this->composerData['support']['email'] = $support_email;
                    }

                    /** @psalm-suppress MixedAssignment */
                    $support_irc = $this->ask('Please enter IRC channel for support, e.g irc://server/channel');
                    if (filled($support_irc)) {
                        /** @psalm-suppress MixedArrayAssignment */
                        $this->composerData['support']['irc'] = $support_irc;
                    }

                } else {
                    if (is_array($composerData['support-suggestions'])) {
                        /** @psalm-suppress MixedAssignment */
                        foreach ($composerData['support-suggestions'] as $type => $support_suggestion) {
                            /** @psalm-suppress MixedArgument */
                            $support = $this->ask($support_suggestion);
                            if (filled($support)) {
                                /** @psalm-suppress MixedArrayAssignment */
                                $this->composerData['support'][$type] = $support;
                            }
                        }
                    }
                }
            }

            $this->composerData['funding'] = [];
            while ($this->confirm('Add funding details?')) {
                /** @psalm-suppress MixedAssignment */
                $funding_source = $this->choice(
                    'Which type of funding source?',
                    ['github', 'patreon', 'tidelift', 'other'],
                    'github'
                );
                /** @psalm-suppress MixedAssignment */
                $funding_url = $this->ask('Please enter the funding url');
                if (filled($funding_url) && filled($funding_source)) {
                    /** @psalm-suppress MixedArrayAssignment */
                    $this->composerData['funding'][] = [
                        'type' => $funding_source,
                        'url' => $funding_url
                    ];
                }
            }

            unset($this->composerData['license-options'], $this->composerData['provide-suggestions'], $this->composerData['replace-suggestions'], $this->composerData['repositories-suggestions'], $this->composerData['support-suggestions']);

            /*
             * This all took far longer than I expected, mainly because I got a bit side-tracked by psalm - so I'm going to leave it for now…
             * This all started because I couldn't find the package:rename command in the original package I forked.
             * I contemplated not writing the `composer.json` out - but I decided in the end it's better to do that and prompt
             * the developer for the rest to be filled out either manually or via the command line.
             *
             * TODO: ask for require - easier from the command line?
             * TODO: ask for require-dev - easier from the command line?
             * TODO: ask for conflict - might take a while?
             * TODO: ask for suggest - low-hanging fruit?
             * TODO: ask for autoload - easier if the devs update this themselves if needed?
             * TODO: ask for autoload-dev - easier if the devs update this themselves if needed?
             * TODO: ask for minimum-stability - low-hanging fruit?
             * TODO: ask for prefer-stable - low-hanging fruit?
             * TODO: ask for repositories - might take a while?
             * TODO: ask for config - might take a while?
             *
             */

            $json = json_encode($this->composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            if ( ! $this->confirm('Promise you’ll read and update the `composer.json` file we are about to create manually?', true)) {
                $this->info('Aborting...');
                $this->newLine();
                return;
            }

            $this->info('  Renaming package...');

            file_put_contents($composerPath, $json);

            $this->info('   composer.json updated successfully.');

        } else {
            $this->error('Could not read composer.json');
        }
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, string>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'package_name' => 'What name (slug) would you like to use for your package?',
            'package_vendor' => 'What vendor name (slug) would you like to use?',
            'package_classname' => 'What classname would you like to replace the word `Package` with (e.g. `MyAwesomePackage`) in the composer file?',
            'package_email' => 'What email address would you like to use?',
        ];
    }

    private function _askForAuthorDetails(): void
    {
        $author = [];

        /** @psalm-suppress MixedAssignment */
        $author_name = $this->ask('Please enter the author name');
        /** @psalm-suppress MixedAssignment */
        $author_email = $this->ask('Please enter the author email');
        /** @psalm-suppress MixedAssignment */
        $author_homepage = $this->ask('Please enter the author homepage (optional)');
        /** @psalm-suppress MixedAssignment */
        $author_role = $this->ask('Please enter the author role (optional)');

        if (filled($author_name)) {
            /** @psalm-suppress MixedAssignment */
            $author['name'] = $author_name;
        }
        if (filled($author_email)) {
            /** @psalm-suppress MixedAssignment */
            $author['email'] = $author_email;
        }
        if (filled($author_homepage)) {
            /** @psalm-suppress MixedAssignment */
            $author['homepage'] = $author_homepage;
        }
        if (filled($author_role)) {
            /** @psalm-suppress MixedAssignment */
            $author['role'] = $author_role;
        }
        if (filled($author)) {
            /** @psalm-suppress MixedArrayAssignment */
            $this->composerData['authors'][] = $author;
        }
    }
}
