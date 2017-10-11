@extends('app')
@section('title')
    Usuarios
@endsection
@section('my_styles')
    <!-- Datatables -->
    {!! Html::style('assets/mine/css/datatable-bootstrap.css') !!}
    {!! Html::style('assets/mine/css/responsive.bootstrap.min.css') !!}
    <style>
        .search{
            font-weight : normal;
            color : #000; 
            font-size: x-large; 
            text-align: center ;
            height: 45px ;       
        }
    </style>
@endsection
@section('content') 
    @include('errors.msgAll')
    <div class="x_panel">
        <div class="x_title">
                <div class="col-md-2">
                    {!! Form::open([ 'route' => 'monitoreo.index', 'method' => 'GET']) !!}
                        {!! Form::text('fecha', $fecha, ['class' => 'form-control search', 'id' => 'fecha', 'autocomplete' => 'off', 'placeholder' => '01-02-2017' ]) !!}
                    {!! Form::close() !!}
                </div>
                <div class="col-md-2">
                    <a class="btn btn-success btn-lg" href="#" onClick="totalCapturas()" class="button" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Descargar"> <i class="fa fa-download"></i> Resumén de capturas </a>
                </div>
                @if(count($data2)>0)
                    <div class="col-md-8" style="text-align:right;">
                        <a class="btn btn-info btn-lg" href="#" onClick="verPdf()" class="button" data-toggle="tooltip" data-placement="bottom" title="" data-original-title=".Pdf"> <i class="fa fa-file-pdf-o"></i> Vista Previa </a>
                        <a class="btn btn-warning btn-lg" href="#" onClick="imprimirPdf()" class="button" data-toggle="tooltip" data-placement="bottom" title="" data-original-title=".Pdf"> <i class="fa fa-print"></i> Imprimir</a>
                    </div>
                @endif
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
             @include('monitoreo.list')
        </div>
    </div>
@endsection
@section('my_scripts')
    <!-- Datatables -->
    {!! Html::script('assets/vendors/datatables.net/js/jquery.dataTables.js') !!}
    {!! Html::script('assets/vendors/datatables.net/js/jquery.dataTables.min.js') !!}
    {!! Html::script('assets/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') !!}
    {!! Html::script('assets/mine/js/dataTables/dataTables.responsive.min.js') !!}
    {!! Html::script('assets/mine/js/dataTables/responsive.bootstrap.js') !!}
    <!-- Pdfmake -->
    {!! Html::script('assets/vendors/pdfmake/build/pdfmake.min.js') !!}
    {!! Html::script('assets/vendors/pdfmake/build/vfs_fonts.js') !!}
    <!-- Mine -->
    {!! Html::script('assets/mine/js/images.js') !!}

    <script>
        var data2   = $.parseJSON(escaparCharEspeciales('{{json_encode($data2)}}'));
        var usuario = $.parseJSON(escaparCharEspeciales('{{json_encode($usuario)}}'));
        var documentoDefinicion = construirTabla();

        // MASCARA TIPO DD-MM-AAAA
        $("#fecha").mask("99-99-9999");

        function totalCapturas() {
            $.get('persona/captura', {}, function(response, status){ // Consulta esquema
                if(response.data==null){
                    notificar('Error','Sin datos','error',2000);
                } else { 
                    construirCapturas(response);
                }
            }).fail(function(){ 
                notificar('Error','Error interno','error',2000);
            });
        }

        function construirCapturas(response){
            var body = [];
            var data_head = [];
            data_head.push({'text':'CLAVE', 'style':'jurisdiccion'},{'text':'JURISDICCIÓN', 'style':'jurisdiccion'},{'text':'TOTAL', 'style':'jurisdiccion'});
            body.push(data_head);
            $.each(response.data, function( indice, row ) { 
                var data_row = [];             
                data_row.push({'text':row.clave, 'style':'normal'},{'text':row.nombre, 'style':'normal'},{'text':''+row.total, 'style':'captura_jurisdiccion'});
                body.push(data_row);
            });

            var data_foot = [];
            data_foot.push({'text':' ', 'style':'jurisdiccion'},{'text':'TOTAL  ', 'style':'jurisdiccion'},{'text':''+response.total, 'style':'jurisdiccion'});
            body.push(data_foot);

            var documentoCapturas = {
                // a string or { width: number, height: number } OFICIO PIXELS: { width: 1285, height: 816 }
                pageSize: 'A4',
                // by default we use portrait, you can change it to landscape if you wish
                pageOrientation: 'portrait',
                pageMargins: [ 40, 70, 40, 70 ],
                header: {
                    margin: [ 40, 30, 40, 30 ],
                    columns: [
                        { image: logo_sm, width: 85 },
                        { text: 'Avance global de capturas', width: 380, alignment: 'center', bold: true },
                        { image: censia, width: 50 }
                    ]
                },
                content: [
                    {
                        layout: 'lightHorizontalLines', // optional
                        table: {
                            widths:['10%','80%','10%'],
                            body
                        }
                    }
                ],
                styles: {
                    normal: {
                        fontSize: 9,
                        italic: true,
                        alignment: 'center',
                        color: '#545454'
                    },
                    captura_jurisdiccion: {
                        fontSize: 12,
                        alignment: 'right',
                        fontWeight: 'bolder',
                        color: 'tomato',
                        verticalAlign: 'middle'
                    },
                    captura_usuario: {
                        fontSize: 11,
                        alignment: 'center',
                        fontWeight: 'bolder'
                    },
                    jurisdiccion: {
                        fontSize: 12,
                        alignment: 'center',
                        fontWeight: 'bold'
                    },
                    usuario: {
                        fontSize: 11,
                        alignment: 'center',
                        fontWeight: 'bold'
                    }
                }
            }
            pdfMake.createPdf(documentoCapturas).open('Avance global de capturas '+moment().format('DD-MM-YYYY')+'.pdf');
        }

        function verPdf()
        {
            pdfMake.createPdf(documentoDefinicion).open('Monitoreo '+moment().format('DD-MM-YYYY')+'.pdf');
        }

        function imprimirPdf()
        {
            pdfMake.createPdf(documentoDefinicion).print('Monitoreo '+moment().format('DD-MM-YYYY')+'.pdf');
        }

        function descargarPdf()
        {
            pdfMake.createPdf(documentoDefinicion).download('Monitoreo '+moment().format('DD-MM-YYYY')+'.pdf');
        }

        function escaparCharEspeciales(str)
        {
            var map =
            {
                '&amp;': '&',
                '&lt;': '<',
                '&gt;': '>',
                '&quot;': '"',
                '&#039;': "'"
            };
            return str.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function(m) {return map[m];});
        }
        
        function construirTabla() {
            var body = [];
            var columns = 0;
            $.each(data2, function( indice, row ) { 
                if(row.usuarios.length>columns){
                    columns = row.usuarios.length;
                }
            });

            var porcent = Math.round(100 / (columns + 2));
            $.each(data2, function( indice, row ) { 
                var data_row = [];  
                var data_row2 = [];  
                var cj = parseInt(row.captura_jurisdiccion);            
                data_row.push({'rowSpan':2, 'text':row.nombre, 'style':'jurisdiccion'},{'rowSpan':2, 'text':''+row.captura_jurisdiccion+'', 'style':'captura_jurisdiccion'});
                data_row2.push({'text':' '},{'text':' '});
                var col = 0;
                $.each(row.usuarios, function( ind, row_usuarios ) {
                    col++;
                    data_row.push({'text':row_usuarios.nombre+' '+row_usuarios.paterno+' '+row_usuarios.materno+' \n '+row_usuarios.email, 'style':'normal'});
                    data_row2.push({'text':''+row_usuarios.captura+'', 'style':'captura_usuario'});
                });

                for (var i = (col + 1); i < (columns + 1); i++) {
                    data_row.push({'text': ' -- ', 'style':'normal'});
                    data_row2.push({'text': ' -- ', 'style':'captura_usuario'});
                }
                body.push(data_row);
                body.push(data_row2);
            });

            return documentoDefinicion = {
                // a string or { width: number, height: number } OFICIO PIXELS: { width: 1285, height: 816 }
                pageSize: 'LEGAL',
                // by default we use portrait, you can change it to landscape if you wish
                pageOrientation: 'landscape',
                pageMargins: [ 40, 70, 40, 70 ],
                header: {
                    margin: [ 40, 30, 40, 30 ],
                    columns: [
                        { image: logo_sm, width: 85 },
                        { text: 'Monitoreo de capturas del '+moment($("#fecha").val(),'DD-MM-YYYY').format('LL'), width: 770, alignment: 'center', bold: true },
                        { image: censia, width: 50 }
                    ]
                },
                footer: {
                    margin: [ 40, 30, 40, 30 ],                
                    columns: [
                        { text: usuario.nombre+' '+usuario.paterno+' '+usuario.materno+' / '+usuario.email, alignment: 'left' },
                        { text: moment().format('LL'), alignment: 'right' }
                    ]
                },
                content: [
                    {
                        layout: 'lightHorizontalLines', // optional
                        table: {
                            body
                        }
                    }
                ],
                styles: {
                    normal: {
                        fontSize: 9,
                        italic: true,
                        alignment: 'left',
                        color: '#545454'
                    },
                    captura_jurisdiccion: {
                        fontSize: 12,
                        alignment: 'center',
                        fontWeight: 'bolder',
                        color: 'tomato',
                        verticalAlign: 'middle'
                    },
                    captura_usuario: {
                        fontSize: 11,
                        alignment: 'center',
                        fontWeight: 'bolder'
                    },
                    jurisdiccion: {
                        fontSize: 12,
                        alignment: 'center',
                        fontWeight: 'bold'
                    },
                    usuario: {
                        fontSize: 11,
                        alignment: 'center',
                        fontWeight: 'bold'
                    }
                }
            }
        }            
    </script>
@endsection