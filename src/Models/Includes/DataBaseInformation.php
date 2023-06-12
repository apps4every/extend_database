<?php



use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

trait DataBaseInformation
{

    public $tableInformation;
    public $relationsManyToMany;
    
    //constructor del trait
    // Inicializa los atributos:
    //     $this->tableInformation: información de atributos del modelo
    //     $this->fillable: atributos del modelo que pueden actualizarse (por defecto todos)
    //     $this->translatable: atributos que son susceptibles de ser traducidos

    public function initializeDataBaseInformation()
    {
        $this->relationsManyToMany = [];
        // setCurrentAdminSite(); //solo si se usa multi site

        $this->tableInformation = self::atributes_form(self::TABLE);
        
        foreach ($this->tableInformation['fields'] as $key => $field) 
        {
            $this->fillable[]=$field['columnName'];
            if ($field['multiLanguage']=='Yes')
            {
                $this->translatable[]=$field['columnName'];
            }
        }        
    }
    // Se registra la información de la tabla vinculada al modelo
    

    // Obtiene las columnas de una tabla con el detalle de cada columna
    private static function getInformationSchema($tableName)
    {
    	$DB_DATABASE = env("DB_DATABASE", "");

        $query = "SELECT *
                      FROM INFORMATION_SCHEMA.COLUMNS
                      WHERE table_name = '".$tableName."' 
                      AND table_schema='".$DB_DATABASE."' 
                      ORDER BY ordinal_position";
        
        $tableColumnDetails = DB::select($query);
        
        return $tableColumnDetails;
    }

    // Obtiene los datos de una tabla, usado para obtener los registros de una fk, 
    // is deprecated
    private static function getListFromTable($tableName)
    {
        $query = "SELECT *
                      FROM ".$tableName." 
                      ORDER BY 2";
        
        $ListFromTable = DB::select($query);
        
        return $ListFromTable;   
    }

    // La siguiente función es para ser llamada por atributes_form
    private static  function attributes_table($tableName="")
    {
        //Obtiene información de la tabla
        $informationSchema = self::getInformationSchema($tableName);
        // $getHasRelatedFiles = $this->model->getHasRelatedFiles(); //Devuelve true si la tabla tiene otra de ficheros vinculada
        $getHasRelatedFiles=false;        

        //Obtiene datos a mostrar en el formulario
        $sections=[];
        $fields=[];
        foreach ($informationSchema as $key => $attribute) 
        {   
            //obtiene información agregada en el campo comentarios
            $columnComment = explode('#',$attribute->COLUMN_COMMENT);
            $section = $columnComment[0];
            $displayName    = '';
            $displayList    = '';
            $multiLanguage  = '';
            $comment        = '';
            $textArea       = 'No';
            $encrypted       = "No";
            // echo "procesando campo $attribute->COLUMN_NAME\n";
            if (count($columnComment)>1)
            {
                $displayName    = $columnComment[1];
                $displayList    = $columnComment[2];
                $multiLanguage  = $columnComment[3];
                $comment        = $columnComment[4];
                if (count($columnComment)>=5)
                {
                    $textArea = $columnComment[5];
                }
                if (count($columnComment)>=6)
                {
                    $encrypted = $columnComment[6];
                }
            }

            //Agrega la sección actual a la lista de secciones
            if ($section!='No Section')
                $sections[]=$section;
            
            //identifica si el campo es un fk
            $isForeignKey = 0;
            $fk_id = '';
            if (strlen($attribute->COLUMN_NAME)>2) 
            {
                $fk_id = substr($attribute->COLUMN_NAME, -3);
            }
            if ($fk_id=='_id')
            {
                $isForeignKey = 1;
            }

            // Agrega todos los resultado al campo final
            $field  = array("section" =>$section, //Sección por la que se agrupan los campos en un formulario
	                        "displayName" =>$displayName, //Nombre que se muestra en pantalla
	                        "displayList" =>$displayList, //Indicador si el campo se usa o no en un listado
	                        "columnName" =>$attribute->COLUMN_NAME, //nombre equivalente usado en la base de datos
	                        "defaultValue" =>$attribute->COLUMN_DEFAULT, //Valor por defecto de la columna
	                        "dataType" =>$attribute->DATA_TYPE, //tipo de dato usado en la base de datos
	                        "columnType" =>$attribute->COLUMN_TYPE, //principalmente para obtener los valores posibles de un tipo enumerado
	                        "maxLength" =>$attribute->CHARACTER_MAXIMUM_LENGTH, //longitud máxima permitida según el campo en la base de datos
                            "is_nullable" =>$attribute->IS_NULLABLE, //Indica si el campo admite nulos o no (es obligatorio)
	                        "multiLanguage" =>$multiLanguage,
                            "textArea" =>$textArea,
	                        "comment"=>$comment,
                            "encrypted"=>$encrypted,
	                        "isForeignKey" => $isForeignKey, //indica si es campo es una fk a otra tabla
	                        "tablenameForeignKey" => '', //nombre de la tabla a la que hace referencia la fk
	                        'dataForeignKey'=>[] //Información de los campos de la tabla fk
	                        );

            // $field["is_nullable"] = str_replace('NO', "1", $field["required"]);
            // $field["required"] = str_replace('YES', "0", $field["required"]);
            $field["defaultValue"] = str_replace('"', "", $field["defaultValue"]);
            $field["defaultValue"] = str_replace("'", "", $field["defaultValue"]);
            if ($field["defaultValue"]=='NULL')
            {
                $field["defaultValue"]   = '';
            }
            $fields[]=$field;
        }

        if ($getHasRelatedFiles)
        {
            $sections[]="Imágenes y Videos";
        }

        $sections=array_unique($sections);

        return array("sections" => $sections,"fields" => $fields);
    }
    // Obtiene la información para pintar la edición de campos de un formulario
    static function atributes_form($tableName="")
    {
        
        // Obtiene información de los campos de la tabla
        $resultado = self::attributes_table($tableName);
        $sections = $resultado['sections'];
        $fields = $resultado['fields'];

        // De momento no usamos las fk y devolvemos el control
        $generic_table= array("sections" => $sections,"fields" => $fields);
        return $generic_table;

        // obtiene información de las tablas fk
        foreach ($fields as $key => $field) 
        {
            if ($field["isForeignKey"])
            {
                $tablename = substr($field['columnName'], 0,strlen($field['columnName'])-3);

                if (Schema::hasTable($tablename)) 
                {
                    $resultado = self::attributes_table($tablename);    
                    
                    // Se obtienen los valores de la ForeignKey
                    $listValuesFk = self::getListFromTable($tablename);    
                    $resultado['fields']['listValuesFk'] = $listValuesFk;

                    $fields[$key]['tablenameForeignKey'] = $tablename; 
                    $fields[$key]['dataForeignKey'] = $resultado['fields'];
                }
                else
                {
                    $field["isForeignKey"]=0;       
                }
            }
        }
        $generic_table= array("sections" => $sections,"fields" => $fields);
        return $generic_table;
        return;
        
        // Visualización resultado
        foreach ($sections as $key => $section) 
        {
            echo "Section: $section<br>";
            echo "==========================<br>";
            foreach ($fields as $key => $field)    
            {
                if ($field['section']==$section)
                {
                    $resto = '';
                    if ($field['isForeignKey'])
                    {
                        
                        $resto = $field['dataForeignKey'][0]['columnName'];
                    }
                    echo "columnName: ".$field['columnName'].
                         " displayName: ".$field['displayName'].
                         " Tipo: ".$field['dataType'].
                         " maxLength: ".$field['maxLength'].
                         " isForeignKey: ".$field['isForeignKey'].
                         " tablenameForeignKey: ".$field['tablenameForeignKey'].
                         "<br>";
                }
            }
        }
    }

    // obtener contenido de todos los datos susceptibles de ser cargados en una vista lista
    public function ajaxIndex($tableName)
    {
        // $tableInformation = self::atributes_form($tableName);
        $fields = [];
        foreach ($this->tableInformation['fields'] as $key => $field) 
        {
            if ($field['displayList']!='No')
            {
                $fields[] = $field['columnName'];
            }
        }
        

        // $faqCategories = self::select('id','title','position')->get();
        $records = self::select($fields)->get();
        return datatables()->of($records)->toJson();
        // return $faqCategories;
    }

    // Propiedades utilizadas en todos los modelos.
    /*********************************************/
    // Muestra la fecha en modo amigable en función del idioma actual
    public function getPublishedAtAttribute($value): string 
    {
        return Carbon::parse($value)->locale(config("app.locale"))->diffForHumans();
    }

    // Devuelve la ruta de una imagen
    public function imagePath($fieldName='img_file')
    {
        return asset(sprintf('storage/%s/%s',self::IMAGES_PATH,$this->getTranslation($fieldName,app()->getLocale())));
    }

    // Devuelve el valor de un campo en el idioma actual
    public function fieldContent($fieldName='content')
    {
        return sprintf('%s',$this->getTranslation($fieldName,app()->getLocale()));
    }

    // Permite definir búsquedas por slug multi idioma
    public function scopeSlug(Builder $builder,$locale,$slug) {
        // $locale = app()->getLocale();
        // if (session('search[articles]')) {
            $search = transliterator_transliterate("Any-Latin; Latin-ASCII; Lower()",$slug);
            $builder->whereRaw(
                "json_extract(LOWER(slug), \"$.$locale\") = convert(? using utf8mb4) collate utf8mb4_general_ci", $search);
        // }
        return $builder;
    }
}