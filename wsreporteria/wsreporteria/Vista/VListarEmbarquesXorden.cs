using System;
using System.Collections.Generic;
using System.Data.SqlClient;
using System.Linq;
using System.Web;
using wsreporteria.Controller;
using wsreporteria.Entity;

namespace wsreporteria.view
{
    public class VListarEmbarquesXorden : BDconexion
    {
        public List<EListarEmbarquesXorden> ListarEmbarquesXorden(String orden)
        {
            List<EListarEmbarquesXorden> lCListarEmbarquesXorden = null;
            using (SqlConnection con = new SqlConnection(conexion))
            {
                try
                {
                    con.Open();
                    CListarEmbarquesXorden oVListarEmbarquesXorden = new CListarEmbarquesXorden();
                    lCListarEmbarquesXorden = oVListarEmbarquesXorden.ListarEmbarquesXorden(con, orden);
                }
                catch (SqlException)
                {
                    
                }
            }
                return (lCListarEmbarquesXorden);
        }
    }
}