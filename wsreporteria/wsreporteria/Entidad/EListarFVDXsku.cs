using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace wsreporteria.Entity
{
    public class EListarFVDXsku
    {
        public Int32 ROW { get; set; }
        public String ALMACEN { get; set; }
        public String SKU { get; set; }
        public String DESCRIPCION { get; set; }
        public Double CANTIDAD_TOTAL { get; set; }
        public Double MONTO { get; set; }
        public String FAMILIA { get; set; }
        public String FECHA { get; set; }
        public String PERIODO { get; set; }
        public Double VEN_QTY_PROCESADA { get; set; }
        public Double VEN_TOT_PROCESADA { get; set; }
        public Double VEN_QTY_CANCELADA { get; set; }
        public Double VEN_TOT_CANCELADA { get; set; }
        public String GRUPO { get; set; }
        public String COMENTARIO { get; set; }
    }
}