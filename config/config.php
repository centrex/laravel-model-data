<?php

return [
    /*
     * The class name of the data model that holds additional data.
     *
     * The model must be or extend `Centrex\ModelData\Data`.
     */
    'data_model' => Centrex\ModelData\Data::class,

    /*
     * Backward compatibility for older installs that referenced the wrong key.
     */
    'status_model' => Centrex\ModelData\Data::class,

    /*
     * The name of the attribute to access the latest data.
     *
     * You can change this value if you have need a custom data attribute.
     */
    'data_attribute' => 'data',

    /*
     * The name of the column which holds the ID of the model related to the data.
     *
     * You can change this value if you have set a different name in the migration for the model_datas table.
     */
    'model_primary_key_attribute' => 'model_id',
];
