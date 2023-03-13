# API REST PHP.

Api REst creada en PHP sin frameworks, este fue el resultado del seguimiento de un curso sobre api´s en Php sin ayuda de frameworks.

Cabe destacar que el codigo es vulnerable a _Sql Inyection_ con fines educativos.

> Ver Index.php para funcionamiento de la Api.

## Auth - login

#### POST /auth

```
{
 "usuario" :"", -> REQUERIDO
 "password": "" -> REQUERIDO
}
```

## Pacientes

```
GET /pacientes?page=$numeroPagina
GET /pacientes?id=$idPaciente
```

#### POST /pacientes

```
{
"nombre" : "", -> REQUERIDO
"dni" : "", -> REQUERIDO
"correo":"", -> REQUERIDO
"codigoPostal" :"",
"genero" : "",
"telefono" : "",
"fechaNacimiento" : "",
"token" : "" -> REQUERIDO
}
```

#### PUT /pacientes

```
{
"nombre" : "",
"dni" : "",
"correo":"",
"codigoPostal" :"",
"genero" : "",
"telefono" : "",
"fechaNacimiento" : "",
"token" : "" , -> REQUERIDO
"pacienteId" : "" -> REQUERIDO
}
```

#### DELETE /pacientes

```
{
"token" : "", -> REQUERIDO
"pacienteId" : "" -> REQUERIDO
}
```
