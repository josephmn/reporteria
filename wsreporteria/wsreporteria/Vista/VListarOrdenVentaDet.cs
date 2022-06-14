using System;
using System.Collections.Generic;
using System.Data.SqlClient;
using System.Linq;
using System.Web;
using wsreporteria.Controller;
using wsreporteria.Entity;

namespace wsreporteria.view
{
    public class VListarOrdenVentaDet : BDconexion
    {
        public List<EListarOrdenVentaDet> ListarOrdenVentaDet(String orden)
        {
            List<EListarOrdenVentaDet> lCListarOrdenVentaDet = null;
            using (SqlConnection con = new SqlConnection(conexion))
            {
                try
                {
                    con.Open();
                    CListarOrdenVentaDet oVListarOrdenVentaDet = new CListarOrdenVentaDet();
                    lCListarOrdenVentaDet = oVListarOrdenVentaDet.ListarOrdenVentaDet(con, orden);
                }
                catch (SqlException)
                {
                    
                }
            }
                return (lCListarOrdenVentaDet);
        }
    }
}