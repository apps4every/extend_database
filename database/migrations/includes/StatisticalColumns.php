<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait StatisticalColumns
{
    // -----------------------------------------------------------------------------------------
    // Statistical Common Columns
    protected function addStatisticalColumns(string $tableName)
    {
        Schema::table($tableName, function($table)
        {
            $table->softDeletes();
        });

        Schema::table($tableName, function($table)
        {
            //Añade indicador de activo sobre el registro
            $comment = array(
                'Section'=>'General',
                'Name'=>'Activa',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Elemento activo, para poder usarse o mostrarse en la web',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('active')
                  ->default(true)
                  ->nullable()
                  ->comment($commentString)
                  ->before('created_at'); //indica si el elemento está activo (se muestra en la web) o desactivo (se oculta)
            
            //Añade registros de usuarios que realizan los cambios
            $comment = array(
                'Section'=>'No Section',
                'Name'=>'Creado por',
                'ViewList'=>'No', //Yes/No/Hidden
                'MultiLanguage'=>'No',
                'Comment'=>'Usuario creador del contenido',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->unsignedBigInteger('created_by')->default(0)->comment($commentString)->nullable()->after('created_at');

            $comment = array(
                'Section'=>'No Section',
                'Name'=>'Modificado por',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Usuario que modificó este contenido',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->unsignedBigInteger('updated_by')->default(0)->comment($commentString)->nullable()->after('updated_at');

            $comment = array(
                'Section'=>'No Section',
                'Name'=>'Marcado como borrado por',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Usuario que marcó como eliminado este contenido',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->unsignedBigInteger('deleted_by')->default(0)->comment($commentString)->nullable()->after('deleted_at');

            // Añade comentarios a las tablas de fechas
            $comment = array(
                'Section'=>'No Section',
                'Name'=>'Fecha Modificación',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Fecha de la última modificación del contenido',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            // $commentString = 'No Section#Fecha Modificación#No#No#Fecha de la última modificación del contenido';
            $table->datetime('updated_at', 0)->comment($commentString)->change();
            $comment = array(
                'Section'=>'No Section',
                'Name'=>'Fecha Creación',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Fecha de creación del contenido',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            // $commentString = 'No Section#Fecha Creación#No#No#Fecha de creación del contenido';
            $table->datetime('created_at', 0)->comment($commentString)->change();
            $comment = array(
                'Section'=>'No Section',
                'Name'=>'Fecha Borrado',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Fecha en que se puso el contenido en la papelera.',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            // $commentString = 'No Section#Fecha Borrado#No#No#Fecha en que se puso el contenido en la papelera.';
            $table->datetime('deleted_at', 0)->comment($commentString)->change();
        });
    }

    // -----------------------------------------------------------------------------------------
    // Add comment to a table (Only for MySql)
    protected function addTableComment(string $tableName,string $comment)
    {
        switch ((new \ReflectionClass(Schema::getConnection()))->getShortName()) 
        {
            case 'MySqlConnection':
                $dBStatement = "ALTER TABLE `".$tableName."` comment '".$comment."'" ;
                \DB::statement($dBStatement);
                break;
            case 'PostgresConnection':
                break;
            case 'SQLiteConnection':
                break;
            case 'SqlServerConnection':
                break;
        }
    }
    // -----------------------------------------------------------------------------------------
    // Common columns on access to content
    protected function addAccessContentColumns(string $tableName)
    {
        Schema::table($tableName, function($table)
        {

            // Añade columnas para información de acceso a los contenidos
            $comment = array(
                'Section'=>'Acceso Contenido',
                'Name'=>'No permite indexar este contenido',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Si el valor es Sí, se incluye el texto <meta name = "robots" content = "nofollow"',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('nofollow')
                  ->default(false)
                  ->nullable()
                  ->comment($commentString)
                  ->before('created_at'); 


            $comment = array(
                'Section'=>'Acceso Contenido',
                'Name'=>'Bloqueo copia contenido',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica si bloquea que el usuario pueda copiar y pegar los textos del contenido',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('block_copy_content')
                  ->default(false)
                  ->nullable()
                  ->comment($commentString)
                  ->before('nofollow'); 

            $comment = array(
                'Section'=>'Bloqueo contenido',
                'Name'=>'Bloqueo Imprimir',
                'ViewList'=>'Yes',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica si bloquea que el usuario pueda imprimir los textos de la web',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('block_print_content')
                  ->nullable()
                  ->comment($commentString)
                  ->before('block_copy_content'); 
            
            $comment = array(
                'Section'=>'Bloqueo contenido',
                'Name'=>'Url para contenidos impresos',
                'ViewList'=>'Yes',
                'MultiLanguage'=>'Yes',
                'Comment'=>'Muestra una url donde podrá adquirir contenidos para imprimir (PDF)',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->json('block_print_content_url')
                  ->nullable()
                  ->comment($commentString)
                  ->before('block_print_content'); 
        });
    }
    // -----------------------------------------------------------------------------------------
    // Common columns on module maintenance mode
    protected function addMaintenanceContentColumns(string $tableName)
    {
        Schema::table($tableName, function($table)
        {

            // Añade columnas para información de modo mantenimiento del módulo
            $comment = array(
                'Section'=>'Modo Mantenimiento',
                'Name'=>'Poner en mantenimiento',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica si el módulo está en mantenimiento y se muestra un mensaje indicando las circunstancias',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('maintenance')
                  ->default(false)
                  ->nullable()
                  ->comment($commentString)
                  ->before('created_at'); 

            $comment = array(
                'Section'=>'Modo Mantenimiento',
                'Name'=>'Texto Web en mantenimiento',
                'ViewList'=>'Yes',
                'MultiLanguage'=>'Yes',
                'Comment'=>'Texto que explica por qué la web está en mantenimiento',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->json('site_maintenance_text')
                  ->nullable()
                  ->comment($commentString)
                  ->before('maintenance'); 

            $comment = array(
                'Section'=>'Modo Mantenimiento',
                'Name'=>'Página de mantenimiento',
                'ViewList'=>'No',
                'MultiLanguage'=>'Yes',
                'Comment'=>'Indica qué página usar para mostrar que el módulo está en mantenimiento',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->unsignedBigInteger('maintenance_pages_id')
                  ->nullable()
                  ->comment($commentString)
                  ->before('site_maintenance_text'); ;
            $table->foreign('maintenance_pages_id')
                  ->references('id')
                  ->on('general_pages');

            $comment = array(
                'Section'=>'Modo Mantenimiento',
                'Name'=>'Usar cuenta atrás disponibilidad módulo',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica si faltan X días para la apertura del módulo',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('countdown')
                  ->default(false)
                  ->nullable()
                  ->comment($commentString)
                  ->before('maintenance_pages_id'); 

            $comment = array(
                'Section'=>'Modo Mantenimiento',
                'Name'=>'Fecha dispone de acceso al módulo',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Fecha en que se dispone de acceso al módulo',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->date('countdown_date')
                  ->nullable()
                  ->comment($commentString)
                  ->before('countdown'); 

            
        });
    }    
    // -----------------------------------------------------------------------------------------
    // Common columns on access restrictions to module content
    protected function addRestrictContentColumns(string $tableName)
    {
        Schema::table($tableName, function($table)
        {
            // Add columns for information on access restrictions to module content
            $comment = array(
                'Section'=>'Restricción acceso',
                'Name'=>'Requiere usuario registrado para leer',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica que el lector necesita estar registrado para leer el contenido, se muestra parte del contenido solamente',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('requires_registration_to_read')
                  ->default(false)
                  ->nullable()
                  ->comment($commentString)
                  ->before('created_at'); 

            $comment = array(
                'Section'=>'Restricción acceso',
                'Name'=>'Requiere registro para acceder al contenido',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica que el lector necesita estar registrado para acceder al contenido, no se muestra el contenido sino un formulario de registro.',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('requires_registration_to_access')      
                  ->default(false)
                  ->nullable()
                  ->comment($commentString)
                  ->before('requires_registration_to_read'); 

            $comment = array(
                'Section'=>'Restricción acceso',
                'Name'=>'Requiere suscripción para leer el contenido',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica que el lector necesita estar pagando una suscripción para leer el contenido, se muestra parte del contenido solamente.',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('requires_subscription_to_read')      
                  ->default(false)
                  ->nullable()
                  ->comment($commentString)
                  ->before('requires_registration_to_access'); 

            $comment = array(
                'Section'=>'Restricción acceso',
                'Name'=>'Requiere suscripción para acceder al contenido',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica que el lector necesita estar registrado para leer el contenido, no se muestra el contenido sino un formulario de registro y/o pago. La diferencia respecto a requires_registration_to_access, es que además de estar registrado debe pagar.',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('requires_subscription_to_access')      
                  ->default(false)
                  ->nullable()
                  ->comment($commentString)
                  ->before('requires_subscription_to_read'); 
 
            $comment = array(
                'Section'=>'Restricción acceso',
                'Name'=>'Visibilidad',
                'ViewList'=>'Hidden',
                'MultiLanguage'=>'No',
                'Comment'=>'indica la visibilidad disponible:  Public,Private,Password,Redirect_Url',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->unsignedBigInteger('general_visibilities_id')
                  ->nullable()
                  ->before('requires_subscription_to_access')
                  ->comment($commentString);
            $table->foreign('general_visibilities_id')
                  ->references('id')
                  ->on('general_visibilities');


            $comment = array(
                'Section'=>'Restricción acceso',
                'Name'=>'Contraseña de acceso al contenido',
                'ViewList'=>'No',
                'MultiLanguage'=>'Yes',
                'Comment'=>'La contraseña de acceso en caso de que el contenido esté protegido',
                'TextArea' => 'No',
                'Encrypted' => 'Yes');
            $commentString = implode("#",$comment);
            $table->string('visibility_password')      
                  ->default(false)
                  ->nullable()
                  ->comment($commentString)
                  ->before('visibility'); 

            $comment = array(
                'Section'=>'Restricción acceso',
                'Name'=>'Página Redirección Url',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Página destino a la que se envía al usuario. Sólo aplicable si la visibilidad es redirección a otra página',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->unsignedBigInteger('visibility_id')
                  ->nullable()
                  ->comment($commentString)
                  ->before('visibility_password'); 
            $table->foreign('visibility_id')
                  ->references('id')
                  ->on('general_pages');
 
        });
    }
    // -----------------------------------------------------------------------------------------
    // Common Columns on Access Restrictions to Module Content Comments
    protected function addCommentsRestrictContentColumns(string $tableName)
    {
        Schema::table($tableName, function($table)
        {
            $comment = array(
                'Section'=>'Restricción comentarios',
                'Name'=>'Habilitar comentarios',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica si los comentarios están habilitados en este contenido.',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('comments_allowed')
                  ->default(false)
                  ->nullable()
                  ->comment($commentString)
                  ->before('created_at'); 

            $comment = array(
                'Section'=>'Restricción comentarios',
                'Name'=>'Control spam',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica si se desea habilitar el control de spam para los comentarios',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('comments_spam_control')
                  ->default(false)
                  ->nullable()
                  ->comment($commentString)
                  ->before('comments_allowed'); 

            $comment = array(
                'Section'=>'Restricción comentarios',
                'Name'=>'Autopublicar comentario',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica si se desea que los comentarios estén aprobados por defecto.',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('comments_autopublish')
                  ->default(false)
                  ->nullable()
                  ->comment($commentString)
                  ->before('comments_allowed'); 

            $comment = array(
                'Section'=>'General',
                'Name'=>'Validador del comentario',
                'ViewList'=>'Yes',
                'MultiLanguage'=>'No',
                'Comment'=>'En caso de ser necesario, usuario que debe validar el comentario',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->unsignedBigInteger('validating_comment_user_id')
                  ->nullable()
                  ->comment($commentString)
                  ->before('comments_autopublish'); 
            $table->foreign('validating_comment_user_id')
                  ->references('id')
                  ->on('users');

            $comment = array(
                'Section'=>'Restricción comentarios',
                'Name'=>'Usuario registrado para publicar comentario',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica si el usuario creador del contenido, debe estar previamente registrado.',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('comments_registered_user_id')
                  ->default(false)
                  ->nullable()
                  ->comment($commentString)
                  ->before('validating_comment_user_id'); 

            $comment = array(
                'Section'=>'Restricción comentarios',
                'Name'=>'Requiere registro para leer comentario',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica que el lector necesita estar registrado para acceder al contenido, no se muestra el contenido sino un formulario de registro.',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('comments_registration_to_access')      
                  ->default(false)
                  ->nullable()
                  ->comment($commentString)
                  ->before('comments_registered_user_id');

        });
    }
    // -----------------------------------------------------------------------------------------
    // Common columns on access restrictions to module content
    protected function addIndexContentColumns(string $tableName)
    {
        Schema::table($tableName, function($table)
        {
            // Add columns for information on access restrictions to module content
            $comment = array(
                'Section'=>'Indexación contenido',
                'Name'=>'Mostrar índice de contenidos',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica si se desea crear un índice en la cabecera del contenido a partir de etiquetas h2 y h3, aplica a todos los contenidos, salvo que se indique lo contrario en alguno en particular',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('create_index')
                  ->default(false)
                  ->nullable()
                  ->comment($commentString)
                  ->before('created_at'); 

            $comment = array(
                'Section'=>'Indexación contenido',
                'Name'=>'Mostrar firma autor',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica si se desea mostrar el detalle del autor del contenido',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('show_sign_author')
                  ->default(false)
                  ->nullable()
                  ->comment($commentString)
                  ->before('create_index'); 
        });
    }
    // -----------------------------------------------------------------------------------------
    // Common columns on access restrictions to module content
    protected function addPublishContentColumns(string $tableName)
    {
        Schema::table($tableName, function($table)
        {
            $comment = array(
                'Section'=>'Publicación contenido',
                'Name'=>'Fecha para publicar el contenido',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Fecha en que se desea publicar el contenido, por defecto, la fecha actual',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->date('publication_date')
                  ->nullable()                  
                  ->useCurrent()
                  ->comment($commentString)
                  ->before('created_at'); 

            $comment = array(
                'Section'=>'Publicación contenido',
                'Name'=>'Notificar publicación de contenido',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Notificar a los administradores la publicación del contenido',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->date('publication_email_administrators')
                  ->nullable()                  
                  ->comment($commentString)
                  ->before('publication_date'); 


            $comment = array(
                'Section'=>'Publicación contenido',
                'Name'=>'Publicar en redes sociales',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Publicar en redes sociales una copia del contenido. TO DO: identificar y ofrecer redes posibles',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->date('publication_social_media')
                  ->nullable()                  
                  ->comment($commentString)
                  ->before('publication_date'); 


            $comment = array(
                'Section'=>'Publicación contenido',
                'Name'=>'Publicar en newsletter una copia del contenido',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Publicar en newsletter una copia del contenido, envía a los suscriptores un aviso del nuevo contenido',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->date('publicaction_news_letter')
                  ->nullable()                  
                  ->comment($commentString)
                  ->before('publication_social_media');

        });
    } 
    // -----------------------------------------------------------------------------------------
    // Common columns on access restrictions to module content
    protected function addExpirationContentColumns(string $tableName)
    {
        Schema::table($tableName, function($table)
        {

            // Añade columnas para información de restricciones de acceso al contenido del módulo
            $comment = array(
                'Section'=>'Expiración contenido',
                'Name'=>'El contenido caduca',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'indica si el contenido expira en un plazo indicado.',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('expiration_enabled')
                  ->default(false)
                  ->nullable()
                  ->comment($commentString)
                  ->before('created_at'); 

            $comment = array(
                'Section'=>'Expiración contenido',
                'Name'=>'Fecha en que el contenido caduca',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Fecha en que el contenido caduca, pasada dicha fecha no se puede acceder al contenido.',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->date('expiration_date')
                  ->nullable()
                  ->comment($commentString)
                  ->before('expiration_enabled'); 

            $comment = array(
                'Section'=>'Expiración contenido',
                'Name'=>'Forma de expirar contenido',
                'ViewList'=>'Yes',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica que visibilidad tendrá el post\r\n  DRAFT: Borrador, valor por defecto. Está pendiente de ser actualizado\r\n DELETE: Borrado físico y definitivo\r\n REMOVED: Borrado lógico\r\n PRIVATE: Solo administradores y editores pueden ver el contenido\r\nPASSWORD: Cualquier usuario que tenga la contraseña podrá ver el contenido\r\nCATEGORY_CHANGE: Cambia la categoría actual del contenido por la seleccionada\r\nTEXT: en lugar del contenido, muestra el valor del campo TEXT\r\nREDIRECT, directamente redicecciona a la url que se indique (pendiente de ver si es a otro post o una url de otra página)',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->enum('expiration_how', ['DRAFT','DELETE','REMOVED','PRIVATE','PASSWORD','CATEGORY_CHANGE','TEXT','REDIRECT'])
                  ->default('DRAFT')
                  ->comment($commentString)
                  ->before('expiration_date'); 

            $comment = array(
                'Section'=>'Expiración contenido',
                'Name'=>'Nueva categoría si el contenido expira',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica la categoría que se asignará si el contenido expira.',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->string('expiration_category')
                  ->nullable()
                  ->comment($commentString)
                  ->before('expiration_how');

            $comment = array(
                'Section'=>'Expiración contenido',
                'Name'=>'Aviso expiración contenido al creador',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica si al expirar el contenido se envía una alerta por mail al creador del contenido',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('expiration_email_owner')
                  ->default(false)
                  ->nullable()
                  ->comment($commentString)
                  ->before('expiration_category'); 

            $comment = array(
                'Section'=>'Expiración contenido',
                'Name'=>'Aviso expiración contenido a administradores',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica si al expirar el contenido se envía una alerta por mail a los adminsitradores',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('expiration_email_administrators')
                  ->default(false)
                  ->nullable()
                  ->comment($commentString)
                  ->before('expiration_category');

            $comment = array(
                'Section'=>'Expiración contenido',
                'Name'=>'Aviso expiración contenido a un email concreto',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Indica si al expirar el contenido se envía una alerta por mail a una cuenta en particular',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->string('expiration_email_who')
                  ->nullable()
                  ->comment($commentString)
                  ->before('expiration_category'); 

            $comment = array(
                'Section'=>'Modo Mantenimiento',
                'Name'=>'Página alternativa al del contenido expirado',
                'ViewList'=>'No',
                'MultiLanguage'=>'Yes',
                'Comment'=>'Al expirar se re-envía al usuario a otro contenido, evitando un error 404',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->unsignedBigInteger('expiration_redirect_pages_id')
                  ->nullable()
                  ->comment($commentString)
                  ->before('expiration_email_who'); ;
            $table->foreign('expiration_redirect_pages_id')
                  ->references('id')
                  ->on('general_pages');

            $comment = array(
                'Section'=>'Modo Mantenimiento',
                'Name'=>'Texto alternativo al del contenido expirado',
                'ViewList'=>'No',
                'MultiLanguage'=>'Yes',
                'Comment'=>'Al expirar se muestra el texto indicado en lugar del contenido original, evitando un error 404',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->json('expiration_text')
                  ->nullable()
                  ->comment($commentString)
                  ->before('expiration_redirect_pages_id'); 
        });
    }
    // -----------------------------------------------------------------------------------------
    // Common columns about in-content advertising
    protected function addAdsContentColumns(string $tableName)
    {
        //Add active indicator on the record
        $comment = array(
                'Section'=>'General',
                'Name'=>'Usar anuncios al mostrar información en la web',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Por defecto indica si se incluiran publicidad en los blog.',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->boolean('use_ads')
                  ->default(false)
                  ->comment($commentString); 
    }
    // -----------------------------------------------------------------------------------------
    // Common SEO Columns
    protected function addSeoColumns(string $tableName)
    {
        Schema::table($tableName, function($table)
        {
            // Add columns for desktop SEO information
            $comment = array(
                'Section'=>'SEO',
                'Name'=>'Slug',
                'ViewList'=>'No',
                'MultiLanguage'=>'Yes',
                'Comment'=>'Identificación para SEO (url friendly,máximo 100 caracteres)',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->json('slug')
                  ->nullable()
                  ->comment($commentString)
                  ->before('created_at'); //Información del título para SEO 

            $comment = array(
                'Section'=>'SEO',
                'Name'=>'Título',
                'ViewList'=>'No',
                'MultiLanguage'=>'Yes',
                'Comment'=>'Valor SEO para el título (máximo 60 caracteres)',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->json('seo_title')
                  ->nullable()
                  ->comment($commentString)
                  ->before('slug'); //Información del título para SEO 

            $comment = array(
                'Section'=>'SEO',
                'Name'=>'Descripción',
                'ViewList'=>'No',
                'MultiLanguage'=>'Yes',
                'Comment'=>'Valor SEO para la descripción (máximo 155 caracteres)',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->json('seo_description')->nullable()->comment($commentString)->after('seo_title'); //Información descripción para SEO

            $comment = array(
                'Section'=>'SEO',
                'Name'=>'Keywords',
                'ViewList'=>'No',
                'MultiLanguage'=>'Yes',
                'Comment'=>'Valor Keywords (máximo 155 caracteres)',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->json('seo_keywords')->nullable()->comment($commentString)->after('seo_title'); //palabras clave relacionadas
            
            $comment = array(
                'Section'=>'SEO',
                'Name'=>'Robots',
                'ViewList'=>'No',
                'MultiLanguage'=>'Yes',
                'Comment'=>'Indica si desea indexar en buscadores la página. Valores posibles a combinar:index, noindex, follow, nofollow">',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->string('seo_robots')
                  ->default('index, follow')
                  ->nullable()
                  ->comment($commentString)->after('seo_description');             

            // Añade columnas para SEO de redes sociales, más información en:
            // https://ogp.me
            // https://ahrefs.com/blog/seo-meta-tags/
            $comment = array(
                'Section'=>'SEO',
                'Name'=>'Título OG',
                'ViewList'=>'No',
                'MultiLanguage'=>'Yes',
                'Comment'=>'Valor SEO para el título en redes sociales (máximo 60 caracteres)',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->json('og_title')
                  ->nullable()
                  ->comment($commentString)->after('seo_robots'); 

            $comment = array(
                'Section'=>'SEO',
                'Name'=>'Descripción OG',
                'ViewList'=>'No',
                'MultiLanguage'=>'Yes',
                'Comment'=>'Valor SEO para la descripción en redes sociales (máximo 155 caracteres)',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->json('og_description')->nullable()->comment($commentString)->after('og_title'); 
            
            $comment = array(
                'Section'=>'SEO',
                'Name'=>'Url OG',
                'ViewList'=>'No',
                'MultiLanguage'=>'Yes',
                'Comment'=>'Valor SEO para la url del elemento en redes sociales (poner url absoluta)',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->json('og_url')->nullable()->comment($commentString)->after('og_description');
                        
            $comment = array(
                'Section'=>'SEO',
                'Name'=>'Imagen del elemento en redes sociales',
                'ViewList'=>'No',
                'MultiLanguage'=>'Yes',
                'Comment'=>'Valor SEO para la imagen del elemento en redes sociales 1200x630 px(poner url absoluta, mismas dimensiones para todos los idiomas)',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->json('img_file_og_image')->nullable()->comment($commentString)->after('og_url');

            $comment = array(
                'Section'=>'SEO',
                'Name'=>'Ancho de la imagen OG',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Ancho de la imagen en pixels de la imagen en redes sociales (1200 px recomendado)',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->integer('og_image_width')->nullable()->comment($commentString)->after('img_file_og_image');

            $comment = array(
                'Section'=>'SEO',
                'Name'=>'Alto de la imagen OG',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Alto de la imagen en pixels de la imagen en redes sociales (630 px recomendado)',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->integer('og_image_height')->nullable()->comment($commentString)->after('og_image_width');

            $comment = array(
                'Section'=>'SEO',
                'Name'=>'Tipo contenido OG',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Tipo del contenido para identificarlo en redes sociales',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->enum('og_type', ['website',
                                     'music',
                                     'video',
                                     'article',
                                     'articles',
                                     'book',
                                     'profile'])
                  ->nullable()
                  ->default('website')
                  ->comment($commentString)
                  ->after('og_image_height');
            
            $comment = array(
                'Section'=>'SEO',
                'Name'=>'Idioma y país OG',
                'ViewList'=>'No',
                'MultiLanguage'=>'No',
                'Comment'=>'Idioma y país',
                'TextArea' => 'No',
                'Encrypted' => 'No',
                'Fillable' => 'Yes');
            $commentString = implode("#",$comment);
            $table->string('og_locale')
                  ->default('es_ES')
                  ->nullable()
                  ->comment($commentString)
                  ->after('seo_description');             
            
        
        


            // Las keyword se eliminan debido al nulo valor SEO e incluso perjudicial con bing.
            // $comment = array(
            //     'Section'=>'SEO',
            //     'Name'=>'Keywords',
            //     'ViewList'=>'No',
            //     'MultiLanguage'=>'Yes',
            //     'Comment'=>'Valor SEO para las Keywords (máximo 155 caracteres)');
            // $commentString = implode("#",$comment);
            // $table->json('seo_keywords')->nullable()->comment($commentString)->after('seo_description'); //Keywords para SEO de la propiedad

            // Añade columnas para información de SEO para dispositivos móviles (cambia la longitud máxima)
            // $comment = array(
            //     'Section'=>'SEO Móvil',
            //     'Name'=>'Título Móvil',
            //     'ViewList'=>'No',
            //     'MultiLanguage'=>'Yes',
            //     'Comment'=>'Valor SEO para el título, dispositivo móvil (máximo 50 caracteres)');
            // $commentString = implode("#",$comment);
            // $table->json('seo_title_mobile')->nullable()->comment($commentString)->after('seo_keywords'); //Información del título para SEO 

            // $comment = array(
            //     'Section'=>'SEO Móvil',
            //     'Name'=>'Descripción Móvil',
            //     'ViewList'=>'No',
            //     'MultiLanguage'=>'Yes',
            //     'Comment'=>'Valor SEO para la descripción, dispositivo móvil (máximo 115 caracteres)');
            // $commentString = implode("#",$comment);
            // $table->json('seo_description_mobile')->nullable()->comment($commentString)->after('seo_title_mobile'); //Información descripción para SEO 

            // $comment = array(
            //     'Section'=>'SEO Móvil',
            //     'Name'=>'Keywords Móvil',
            //     'ViewList'=>'No',
            //     'MultiLanguage'=>'Yes',
            //     'Comment'=>'Valor SEO para las Keywords, dispositivo móvil (máximo 115 caracteres)');
            // $commentString = implode("#",$comment);
            // $table->json('seo_keywords_mobile')->nullable()->comment($commentString)->after('seo_description_mobile'); //Keywords para SEO 
            
        });
    }       
}
