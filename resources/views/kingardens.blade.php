<DOCTYPE!>
<html>
	<head>
		<meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
    	<link href="css/kindtable.css" rel="stylesheet">
	</head>
	<body>
		<h1>Боғчаларга <span>4 декабр</span> учун <span>менюлар</span></h1>

		<table class="responstable">
		  <tr>
		    <th data-th="Driver details"><span>Т/Р</span></th>
		    <th>Боғча</th>
		    <th>Сана</th>
		    <th>Хужжатлар</th>
		  </tr>
		  
		  @foreach($gardens as $garden)
		  <tr>
		    <!--<td><input type="radio"/></td>-->
		    <td>{{ $loop->index+1 }}</td>
		    <td>{{ $garden->kingar_name }}</td>
		    <td>04/12/2021</td>
		    <td><a href="/showmenu/{{ $garden->id }}/{{ $day->id }}/1">MЕНЮ 3-4 ёш </a><a href="/showmenu/{{ $garden->id }}/{{ $day->id }}/2" style="padding-left: 20px">MЕНЮ 4-7 ёш </a><a href="#" style="padding-left: 20px"> НАКЛАДНАЯ</a></td>
		  </tr>
		  @endforeach
		  
		</table>
	</body>
</html>