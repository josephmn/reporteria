using System;
using System.Collections.Generic;
using System.Configuration;
using System.Linq;
using System.Runtime.Serialization;
using System.Web;

namespace wsreporteria
{
    public class BDconexion
    {
        public string conexion = ConfigurationManager.ConnectionStrings["conexion"].ConnectionString;//VISTAS
    }

    public class RetornoData
    {
        [DataMember]
        public int respuesta { get; set; }
    }

}