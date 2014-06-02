using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Acsilserver1.Model
{
    class LoadListe
    {
        public ItemListe Charger()
        {
            return new ItemListe { ID = 0, Type = "/Assets/Icon/camera.png", Name = "voila le fichier.jpg", Date = "18/04/2014", URL = "http://acsilserver.com/wp-content/uploads/2013/08/eip.png" };
        }
    }
}
