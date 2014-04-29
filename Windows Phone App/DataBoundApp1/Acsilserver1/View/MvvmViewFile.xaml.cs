using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Navigation;
using Microsoft.Phone.Controls;
using Microsoft.Phone.Shell;
using Acsilserver1.Resources;
using System.Windows.Data;
using System.Collections.ObjectModel;
using System.ComponentModel;
 
namespace Acsilserver1.View
{
    public class Contexte : INotifyPropertyChanged
    {
        private string url;
        public string URL
        {
            get
            {
                return url;
            }
            set
            {
                if (value == url)
                    return;
                url = value;
                NotifyPropertyChanged("URL");
            }
        }

        private string _name;
        public string Name
        {
            get
            {
                return _name;
            }
            set
            {
                if (value == _name)
                    return;
                _name = value;
                NotifyPropertyChanged("Name");
            }
        }


        public event PropertyChangedEventHandler PropertyChanged;

        public void NotifyPropertyChanged(string nomPropriete)
        {
            if (PropertyChanged != null)
                PropertyChanged(this, new PropertyChangedEventArgs(nomPropriete));
        }
    }

    public partial class MvvmViewFile : PhoneApplicationPage
    {
        // Constructor
        public MvvmViewFile()
        {
            InitializeComponent();

            contexte = new Contexte { URL = "", Name = "" };
            DataContext = contexte;
            // Sample code to localize the ApplicationBar
            //BuildLocalizedApplicationBar();
        }

        private Contexte contexte;

        protected override void OnNavigatedTo(System.Windows.Navigation.NavigationEventArgs e)
        {
            string login;
            if (NavigationContext.QueryString.TryGetValue("Name", out login))
            {
                contexte.Name = login;
                
            }

            string _url;
            if (NavigationContext.QueryString.TryGetValue("URL", out _url))
            {

                contexte.URL = _url;
            }
            Media.Play();

            base.OnNavigatedTo(e);
        }

        private void Media_Tap(object sender, System.Windows.Input.GestureEventArgs e)
        {
            if (Media.CurrentState == System.Windows.Media.MediaElementState.Playing)
            {
                Media.Pause();
            }
            else
            {
                Media.Play();
            }
        }

        protected override void OnBackKeyPress(System.ComponentModel.CancelEventArgs e)
        {
            Media.Stop();
            
            base.OnBackKeyPress(e);
        }
       
    }
}