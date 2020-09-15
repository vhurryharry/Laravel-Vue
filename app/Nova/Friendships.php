<?php

namespace App\Nova;

use App\Profile;
use Hootlex\Friendships\Traits\Friendable;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\MorphToMany;

class Friendships extends Resource
{
    use Friendable;
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Friendship';

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
        'id', 'sender.name'
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

            // MorphTo::make('sender')->types([
            //     Profile::class,
            // ])->hideFromIndex(),

            // MorphToMany::make('Friendable')

            Text::make('Sender', 'sender.name')
                ->sortable()
                ->rules('required', 'max:255'),


            Text::make('Recipient', 'recipient.name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Status')
                ->sortable()
                ->rules('required', 'max:255'),
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
