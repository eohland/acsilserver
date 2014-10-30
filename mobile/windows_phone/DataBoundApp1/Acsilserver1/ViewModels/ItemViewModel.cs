using System;
using System.ComponentModel;
using System.Diagnostics;
using System.Net;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Animation;

namespace Acsilserver1.ViewModels
{
    public class ItemViewModel : INotifyPropertyChanged
    {
        private int _id;
        /// <summary>
        /// Exemple de propriété ViewModel ; cette propriété est utilisée pour identifier l'objet.
        /// </summary>
        /// <returns></returns>
        public int ID
        {
            get
            {
                return _id;
            }
            set
            {
                if (value != _id)
                {
                    _id = value;
                    NotifyPropertyChanged("ID");
                }
            }
        }

        private string _type;
        /// <summary>
        /// Exemple de propriété ViewModel ; cette propriété est utilisée dans la vue pour afficher sa valeur à l'aide d'une liaison.
        /// </summary>
        /// <returns></returns>
        public string Type
        {
            get
            {
                return _type;
            }
            set
            {
                if (value != _type)
                {
                    _type = value;
                    NotifyPropertyChanged("Type");
                }
            }
        }

        private string _shared;
        /// <summary>
        /// Exemple de propriété ViewModel ; cette propriété est utilisée dans la vue pour afficher sa valeur à l'aide d'une liaison.
        /// </summary>
        /// <returns></returns>
        public string Shared
        {
            get
            {
                return _shared;
            }
            set
            {
                if (value != _shared)
                {
                    _shared = value;
                    NotifyPropertyChanged("Shared");
                }
            }
        }

        private string _name;
        /// <summary>
        /// Exemple de propriété ViewModel ; cette propriété est utilisée dans la vue pour afficher sa valeur à l'aide d'une liaison.
        /// </summary>
        /// <returns></returns>
        public string Name
        {
            get
            {
                return _name;
            }
            set
            {
                if (value != _name)
                {
                    _name = value;
                    NotifyPropertyChanged("Name");
                }
            }
        }

        private string _mimetype;
        /// <summary>
        /// Exemple de propriété ViewModel ; cette propriété est utilisée dans la vue pour afficher sa valeur à l'aide d'une liaison.
        /// </summary>
        /// <returns></returns>
        public string MimeType
        {
            get
            {
                return _mimetype;
            }
            set
            {
                if (value != _mimetype)
                {
                    _mimetype = value;
                    NotifyPropertyChanged("MimeType");
                }
            }
        }

        private string _date;
        /// <summary>
        /// Exemple de propriété ViewModel ; cette propriété est utilisée dans la vue pour afficher sa valeur à l'aide d'une liaison.
        /// </summary>
        /// <returns></returns>
        public string Date
        {
            get
            {
                return _date;
            }
            set
            {
                if (value != _date)
                {
                    _date = value;
                    NotifyPropertyChanged("Date");
                }
            }
        }

        private string _url;
        /// <summary>
        /// Exemple de propriété ViewModel ; cette propriété est utilisée dans la vue pour afficher sa valeur à l'aide d'une liaison.
        /// </summary>
        /// <returns></returns>
        public string URL
        {
            get
            {
                return _url;
            }
            set
            {
                if (value != _url)
                {
                    _url = value;
                    NotifyPropertyChanged("URL");
                }
            }
        }

        private string _taille;
        /// <summary>
        /// Exemple de propriété ViewModel ; cette propriété est utilisée dans la vue pour afficher sa valeur à l'aide d'une liaison.
        /// </summary>
        /// <returns></returns>
        public string Taille
        {
            get
            {
                return _taille;
            }
            set
            {
                if (value != _taille)
                {
                    _taille = value;
                    NotifyPropertyChanged("Taille");
                }
            }
        }

        private string _Owner;
        /// <summary>
        /// Exemple de propriété ViewModel ; cette propriété est utilisée dans la vue pour afficher sa valeur à l'aide d'une liaison.
        /// </summary>
        /// <returns></returns>
        public string Owner
        {
            get
            {
                return _Owner;
            }
            set
            {
                if (value != _Owner)
                {
                    _Owner = value;
                    NotifyPropertyChanged("Owner");
                }
            }
        }

        private string _numFile;
        /// <summary>
        /// Exemple de propriété ViewModel ; cette propriété est utilisée dans la vue pour afficher sa valeur à l'aide d'une liaison.
        /// </summary>
        /// <returns></returns>
        public string NumFile
        {
            get
            {
                return _numFile;
            }
            set
            {
                if (value != _numFile)
                {
                    _numFile = value;
                    NotifyPropertyChanged("NumFile");
                }
            }
        }

        public event PropertyChangedEventHandler PropertyChanged;
        private void NotifyPropertyChanged(String propertyName)
        {
            PropertyChangedEventHandler handler = PropertyChanged;
            if (null != handler)
            {
                handler(this, new PropertyChangedEventArgs(propertyName));
            }
        }
    }
}