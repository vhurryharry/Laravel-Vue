<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Webpatser\Countries\CountriesFacade;
use Laravel\Nova\Fields\MorphOne;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\HasOne;
use App\Country;

class Profile extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Profile';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Gravatar::make(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('First name', 'firstname')->hideFromIndex(),

            Text::make('Last name', 'lastname')->hideFromIndex(),

            // Select::make('Gender')->options([
            //     '1' => '2',
            //     '3' => '4',
            // ]),

            BelongsTo::make('User'),

            // HasOne::make('Location'),

            MorphOne::make('Location'),

            // Text::make('Location', 'location.name'),


            // HasOne::make('Location'),

            // Select::make('Locations')->options(CountriesFacade::all()->pluck('name'))->hideFromIndex(),
        ];
    }
    protected function addressFields()
    {
        return [
            // Place::make('Address', 'address_line_1')->hideFromIndex(),
            // Text::make('Address Line 2')->hideFromIndex(),
            // Country::make('Country')->hideFromIndex(),
        ];
    }
    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
