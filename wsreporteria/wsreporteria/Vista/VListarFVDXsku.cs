using System;
using System.Collections.Generic;
using System.Data.SqlClient;
using System.Linq;
using System.Web;
using wsreporteria.Controller;
using wsreporteria.Entity;

namespace wsreporteria.view
{
    public class VListarFVDXsku : BDconexion
    {
        public List<EListarFVDXsku> ListarFVDXsku(Int32 post, String almacen)
        {
            List<EListarFVDXsku> lCListarFVDXsku = null;
            using (SqlConnection con = new SqlConnection(conexion))
            {
                try
                {
                    con.Open();
                    CListarFVDXsku oVListarFVDXsku = new CListarFVDXsku();
                    lCListarFVDXsku = oVListarFVDXsku.ListarFVDXsku(con, post, almacen);
                }
                catch (SqlException)
                {
                    
                }
            }
                return (lCListarFVDXsku);
        }
    }
}