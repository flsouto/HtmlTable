# HtmlTable

They write html tables through the command-line straight to the server.
Hats off to them. For the rest of us, HtmlTable is a great help to our work.

## Utilização básica

Cria e renderiza uma tabela com atributos, colunas e dados.

    $tbl = new Table(['width'=>800]);
    $tbl->col('id');
    $tbl->col('name');
    $tbl->data([
        ['id'=>1,'name'=>'Alucard'],
        ['id'=>2,'name'=>'Richter']
    ]);
    echo $tbl;

Alinhamento de colunas:

    $tbl->col('id')->align('left|center|right');
    $tbl->col('name')->align('left|center|right');    

Definindo a largura de colunas:

    $tbl->col('id')->align('center')->width('1px');
    $tbl->col('name')->align('left')->width('500px');        

Por default o heading da coluna vai ser o nome da chave substituindo "_" por espaço. Para customizar o heading:

    $tbl->col('name')
        ->heading('Nome do Personagem')

Por default todas as colunas são ordenáveis. Para desabilitar ordenação:

    $tbl->col('actions')
        ->sortable(false)

Defina um default para quando o valor de uma coluna for null:

    $tbl->col('dt_updated')->heading('Atualizado')->blank('-- Never ---')


Template customizado para os dados de uma coluna:

    $tbl->col('link')->td('<a href="?id={id}">Ver {name}</a>');

Para customizar a própria tag td, encapsule com a tag TD:

    $tbl->col('name')->td('<td data-id="{id}">{content}</td>');

Passe ARRAY para customizar apenas os atributos da TD:

    $tbl->col('name')->td([
        'data-id' => '{id}',
        'class' => 'whatever'
    ]);

Customizando template + atributos:

    $tbl->col('name')->td('<b>{content}</b>')->td([
        'data-id' => '{id}',
        'class' => 'whatever'
    ]);

A função align() e width() setam alguns atributos internamente. Use {attrs} para reaproveitá-los no template:

    $tbl->col('name')
        ->align('left')
        ->width('500px')
        ->td('<td {attrs} class=blah><b>{content}</b></td>');

**Obs.:** o align() aplica o mesmo estilo tanto na td (célula) como na th (cabeçalho) usando a api de estilos:

    $tbl->col('name')
        ->td([
            'class' => 'blah',
            'style' => ['background-color'=>'blue', 'color'=> 'white']
        ]);

Todas as feature disponíveis para td também estão disponível para th:

    $tbl->col('name')
        ->td(['class'=>'blah'])
        ->th(['style'=>['background'=>'black']]);        

## Features Avançadas

Callback para customizar TD ao ser renderizada:

    $tbl->data([
        ['id'=>1,'name'=>'Alucard','status'=>'dead'],
        ['id'=>2,'name'=>'Richter','status'=>'alive']
    ]);
    $tbl->col('actions')->td(function(Td $td){
        if($td->data->get('status') == 'dead'){
            $td->template('<button>Ressurect {name}</button>');
        } else {
            $td->template('<button>Kill {name}</button>');
        }
    });
    echo $tbl;

Mesma coisa para TH, exceto que ela não possui dados:

    $tbl->col('actions')->th(function(Th $th){
          $th->attrs->set('class','actions-heading');
          $th->attrs->style(['background'=>'yellow']);
    });

Para customizar uma TR, ou seja, uma row da tabela:

    $tbl->tr->callback(function(Tr $tr){
        if($tr->data->get('status') == 'dead'){
            $tr->attrs->set('class', 'dead');
        } else {
            $tr->attrs->set('class', 'alive');
        }
    });

Se a lógica for muito simples como acima, bastaria definir um template:

    $tbl->tr->template('<tr class="{status}" data-id="{id}">{content}</tr>');

Setar os atributos na TR também é valido. O exemplo abaixo gera o mesmo resultado que o exemplo anterior:

    $tbl->tr->attrs->merge([
        'class' => '{status}',
        'data-id' => '{id}'
    ]);    

**Obs:** Ao retornar uma string no `callback` você sobrescreve toda a geração do elemento. Isso vale para Th, Td e Tr:

    $el->callback(function(){
        return "<custom markup>";
    })

As vezes é necessário modificar todas as células (tds) de uma row (tr) dependendo dos seus dados:

    $tbl->each(function(Column $col){
        $col->td(function(Td $td){
            if($td->data->get('status')=='dead'){
                $td(['style'=>['background'=>'red']]);
            } else {
                $td(['style'=>['background'=>'green']]);
            }
        });
    });

O exemplo acima utiliza um recurso de "syntax sugar" para setar os atributos de estilo da td.
Observe que todas as formas abaixo são equivalentes:

    $td(['style'=>'backgorund:red']);
    $td->attrs->set('style', 'background:red');
    $td->attrs->set('style', ['background'=>'red']);
    $td->attrs->style('background:red');
    $td->attrs->style(['background'=>'red']);

Observe também que os resultados são acumulativos:

    $td->attrs->style('background:red;color:white');
    $td->attrs->style('color:green'); // redefine a cor da fonte
    $td(['style'=>['border'=>'1px solid black']);

Esses recursos estão disponíveis em todos os outros elementos que extendem Element, como Tr e Th.
A classe `Table` não estende Element, porém fornece o atributo `$tbl->attrs` que possui as mesmas features.

### Sobrescrevendo os dados de requisição

Por default o objeto table obtem os dados da requesição a partir da variável $_REQUEST.
Passe o segundo parâmetro do construtor para sobrescrever isso:

    $tbl = new Table(['id'=>'whatever'], $my_request_data);

### Customizando a Ordenação

O objeto de tabela expõe a propriedade `$tbl->sorting` que permite:

- Definir ordenação default,
- Pegar a ordenação sendo requisitada
- Gerar um sql seguro para colocar numa cláusula ORDER BY
- Gerar uma url para ordenar alguma coluna levando em conta o estado anterior (ASC/DESC)

Para definir ordenação default:

    $tbl->sorting->defaults('id','DESC');

Para pegar a ordenação sendo requisitada

    $tbl->sorting->current()->col
    $tbl->sorting->current()->ord

O objeto retornado pelo current pode ser concatenado com strings:

    $sql = "SELECT ... ORDER BY ".$tbl->sorting->current()

Caso seja necessário usar um alias para a coluna do SQL:

    $sql = "SELECT ... ORDER BY ".$tbl->sorting->current()->sql('a')

**Obs:** não há necessidade de se preocupar com sql injection
pois o método current() valida o nome da coluna verificando se ela foi definida
na tabela através de um `$tbl->col`. A string ASC/DESC tbm é validada.

O objeto Th de uma Table já utiliza o Sorting para gerar links de ordenação para as colunas.
Mas caso seja necessário gerar a url manualmente, utilize o método `url`:

    $tbl->sorting->url('column')    

## Colunas especiais

Além do tipo padrão (string) `$tbl->col()` exitem colunas para cada tipo relevante de dado ou para construir controles básicos como botões.
É importante utilizar o tipo certo para a coluna pois afeta o sorting e a formatação dos seus dados:

### Numeric

    $tbl->cnum('price')
        ->thousands('.')
        ->precision(2)
        ->point(',')
        ->td('R$ {content}')

### Date

    $tbl->cdate('dt_added')->datef('d/m/Y H:i:s')

### Button

    $tbl->cbtn('Label')
        ->url('action?id={id}', $new_tab=true)
        ->element([
            'class' => 'action-btn',
            'data-id' => '{id}',
            'style' => 'etc...'
        ])

## Colunas inputáveis

### Checkbox

A coluna de Checkbox insere um checkbox por linha. É útil para fazer seleção de valores de alugma coluna:

    $tbl->ccheck('selection[id]');

Neste exemplo, o HtmlTable irá gerar checkboxes para selecionar os ids de uma tabela:

    <input type="checkbox" name="selection[{id}]" />

Então é quase a mesma coisa do que fazer uma coluna normal com o seguinte template:

    $tbl->col('id')->td('<input type="checkbox" name="selection[{id}]" />')

A vantagem do Checkbox é que ele procura automaticamente no $request da tabela para ver se algum checkbox foi marcado,
inserindo o atributo "checked" em cada checkbox correspondente.

Para ver se o checkbox foi marcado:

    if(isset($request['selection'])){
        $selected_ids = array_keys($request['selection']);
    }

Utilize a seguinte técnica para deixar alguns checkboxes marcados por default:

    ...
    if(!$form_submited){
        $request['selection'][1] = true;
        $request['selection'][1] = true;
        $request['selection'][3] = true;
    }
    $tbl = new Table(['width'=>800], $request);
    $tbl->ccheck('id:selection');
    ...

### Sintaxe alternativa para campos inputáveis

Existe uma sintaxe alternativa para definir campos inputáveis:

    $tbl->ccheck('data[id][active]');

Esta alternativa é últil quando você já tem uma coluna booleana nos seus dados e deseja utilizar o valor dela
para marcar o checkbox. Neste caso, os dados enviados pelo form virão da seguinte maneira:

    foreach($request['data'] as $id => $row){
        if(!empty($row['active']){
            // etc...
        }
    }

Essa sintaxe também se torna útil quando você tem múltiplos dados inputáveis na sua tabela:

    $tbl->ccheck('data[id][update]');
    $tbl->col('data[id][name]');
    $tbl->col('data[id][description]');

O processamento fica muito mais elegante:

    foreach($request['data'] as $id => $row){
        if(!empty($row['update']){
            $db->update('table', $data, ['id'=>$id]);
        }
    }    

### Campos inputáveis VS formulários

O HtmlTable não cria automaticamente as tags `<form></form>`.
Então para ser possível submeter os dados inputados em uma tabela, é necessário
renderizá-la dentro de um formulário:

    <form method="METHOD" action="ACTION">
        {{table}}
        <button>Submit</button>
    </form>


## Definindo suas próprias colunas

A função `$tbl->col()` aceita um objeto de coluna pré-definido.
Então basta implementar a sua própria class de coluna, instanciá-la e injetar o objeto:

    class MyColumn extends Column{
        function render(Data $data){
            return "cool stuff";
        }
    }
    
    $tbl->col(new MyColumn)->align('etc..');

## Glosário de Variáveis Internas

Como já deve ter percebido nesse documento existem algumas variáveis pré-definidas
pelo HtmlTable, que podem ser referenciadas em templates. Aqui está um glosário com todas essas variáveis:

- {attrs} - atributos da tag, incluindo style (css)
- {attrs.style} - apenas o atributo style (css)
- {content} - conteudo interno da tag (geralmente é o valor formatado da coluna)
- {table.index} - índice do row sendo renderizado
- {button.label} - label de um botão
- {button.element} - elemento input do botão (classe In)
- {checkbox.element} - elemento input do checkbox (classe In)
