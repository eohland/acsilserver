using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Navigation;
using Microsoft.Phone.Controls;
using Microsoft.Phone.Shell;

namespace HTML5App1
{
    public partial class MainPage : PhoneApplicationPage
    {
        // URL de la page d'accueil
        private string MainUri = "/Html/index.html";

        // Constructeur
        public MainPage()
        {
            InitializeComponent();
        }

        private void Browser_Loaded(object sender, RoutedEventArgs e)
        {
            Browser.IsScriptEnabled = true;

            // Ajoutez votre URL ici
            Browser.Navigate(new Uri(MainUri, UriKind.Relative));
        }

        // Navigue vers l'arrière dans la pile de navigation du navigateur Web mais pas dans les applications.
        private void BackApplicationBar_Click(object sender, EventArgs e)
        {
            Browser.GoBack();
        }

        // Navigue vers l'avant dans la pile de navigation du navigateur Web mais pas dans les applications.
        private void ForwardApplicationBar_Click(object sender, EventArgs e)
        {
            Browser.GoForward();
        }

        // Navigue jusqu'à la page d'accueil initiale.
        private void HomeMenuItem_Click(object sender, EventArgs e)
        {
            Browser.Navigate(new Uri(MainUri, UriKind.Relative));
        }

        // Gère les erreurs de navigation.
        private void Browser_NavigationFailed(object sender, System.Windows.Navigation.NavigationFailedEventArgs e)
        {
            MessageBox.Show("Échec de la navigation vers cette page, vérifiez votre connexion Internet");
        }
    }
}
