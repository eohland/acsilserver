using System;
using System.Collections.ObjectModel;
using System.ComponentModel;
using Acsilserver1.Resources;
using System.Net;
using System.IO;
using Microsoft.Phone.Shell;
using System.Text;
using Newtonsoft.Json.Linq;
using System.Windows.Threading;
using System.Windows;
using Newtonsoft.Json;

namespace Acsilserver1.ViewModels
{
    public class MainViewModel : INotifyPropertyChanged
    {
        public MainViewModel()
        {
            this.Items = new ObservableCollection<ItemViewModel>();
        }

        /// <summary>
        /// Collection pour les objets ItemViewModel.
        /// </summary>
        public ObservableCollection<ItemViewModel> Items { get; private set; }

        private string _sampleProperty = "Sample Runtime Property Value";
        /// <summary>
        /// Exemple de propriété ViewModel ; cette propriété est utilisée dans la vue pour afficher sa valeur à l'aide d'une liaison
        /// </summary>
        /// <returns></returns>
        public string SampleProperty
        {
            get
            {
                return _sampleProperty;
            }
            set
            {
                if (value != _sampleProperty)
                {
                    _sampleProperty = value;
                    NotifyPropertyChanged("SampleProperty");
                }
            }
        }

        /// <summary>
        /// Exemple de propriété qui retourne une chaîne localisée
        /// </summary>
        public string LocalizedSampleProperty
        {
            get
            {
                return AppResources.SampleProperty;
            }
        }

        public bool IsDataLoaded
        {
            get;
            private set;
        }



        private void GetResponseCallback(IAsyncResult rez)
        {
            try
            {
                HttpWebRequest hwr = rez.AsyncState as HttpWebRequest;
                HttpWebResponse response = hwr.EndGetResponse(rez) as HttpWebResponse;
                string a = (new StreamReader(response.GetResponseStream(), Encoding.UTF8)).ReadToEnd();

                //urlserver+"uploads/"+info.pseudo_owner+"/"+info.real_path+info.path
                int index = 0;

                RootObject resultat = JsonConvert.DeserializeObject<RootObject>(a);
                Deployment.Current.Dispatcher.BeginInvoke(() =>
                {
                    int j = 0;
                    while (j != resultat.folders.Count)
                    {

                        Folder foldtest = resultat.folders[j];
                        string _URL = PhoneApplicationService.Current.State["URL"].ToString() + "uploads/" + foldtest.pseudo_owner + "/" + foldtest.real_path + foldtest.path;
                        DateTime dt = Convert.ToDateTime(foldtest.upload_date);
                        this.Items.Add(new ItemViewModel() { NumFile = index.ToString(), ID = foldtest.id, Type = ObtientImage("folder", _URL), Name = foldtest.name, Date = String.Format("{0:MM/dd/yyyy}", dt), URL = _URL, Owner = foldtest.owner, Taille = foldtest.size.ToString() + " fichiers" });
                        j++;
                        index++;
                    }

                    int i = 0;
                    while (i != resultat.files.Count)
                    {
                        File filtesst = resultat.files[i];
                        string _URL = PhoneApplicationService.Current.State["URL"].ToString() + "uploads/" + filtesst.info.pseudo_owner + "/" + filtesst.info.real_path + filtesst.info.path;
                        DateTime dt = Convert.ToDateTime(filtesst.info.upload_date);
                        this.Items.Add(new ItemViewModel() {MimeType= filtesst.info.mime_type, NumFile = index.ToString(), ID = filtesst.info.id, Type = ObtientImage(GetExtansion(filtesst.info.mime_type), _URL), Name = filtesst.info.name, Date = String.Format("{0:MM/dd/yyyy}", dt), URL = _URL, Owner = filtesst.info.owner, Taille = filtesst.info.size.ToString() + "B", Shared= Convert.ToBoolean(filtesst.info.is_shared) ? filtesst.info.owner:"" });
                        index++;
                        i++;
                    }
                    this.IsDataLoaded = true;
                });
            }
            catch (Exception e)
            {

                string a = e.ToString();
            }


        }

        private string GetExtansion(string p)
        {

            if ((p == "jpeg" ) || (p == "png"))
                return "pictures";
            if ((p == "avi") || (p == "mp4"))
                return "video";
            if (p == "mpga")
                return "music";
            if (p == "pdf")
                return "pdf";
            if (p == "zip")
                return ParseExtansion(p);
                

            return null;
        }

        private string ParseExtansion(string p)
        {
            return null;
        }
        /// <summary>
        /// Crée et ajoute quelques objets ItemViewModel dans la collection Items.
        /// </summary>
        public void LoadData(string IdFolder)
        {
            this.IsDataLoaded = false;
            this.Items.Clear();
            string destinationURL = null;

            if (IdFolder == "0")
                destinationURL = PhoneApplicationService.Current.State["URL"].ToString() + "app_dev.php/service/1/op/list";
            else
                destinationURL = PhoneApplicationService.Current.State["URL"].ToString() + "app_dev.php/service/1/op/list/" + IdFolder;
            HttpWebRequest hwr = WebRequest.Create(new Uri(destinationURL)) as HttpWebRequest;
            hwr.Method = "POST";
            hwr.Accept = "application/json";
            hwr.Headers[System.Net.HttpRequestHeader.Authorization] = "Bearer " + PhoneApplicationService.Current.State["token"].ToString();

            hwr.BeginGetResponse(GetResponseCallback, hwr);

        }

        private string ObtientImage(string type, string _URL)
        {
            if (type != null)
                return "/Assets/Icon/" + type + ".png";

            return "/Assets/Icon/erreur.png";
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