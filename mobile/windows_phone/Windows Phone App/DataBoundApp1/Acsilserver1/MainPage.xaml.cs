﻿using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Navigation;
using Microsoft.Phone.Controls;
using Microsoft.Phone.Shell;
using Acsilserver1.Resources;
using System.IO.IsolatedStorage;

namespace Acsilserver1
{
    public partial class MainPage : PhoneApplicationPage
    {
        // Constructeur
        public MainPage()
        {
            InitializeComponent();

           
            

            // Exemple de code pour la localisation d'ApplicationBar
            //BuildLocalizedApplicationBar();
        }


        protected override void OnNavigatedTo(System.Windows.Navigation.NavigationEventArgs e)
        {
            bool isLogged = false;

            if (IsolatedStorageSettings.ApplicationSettings.Contains("logged"))
                isLogged = (bool)IsolatedStorageSettings.ApplicationSettings["logged"];

            if (IsolatedStorageSettings.ApplicationSettings.Contains("Login"))
                Login.Text = (string)IsolatedStorageSettings.ApplicationSettings["Login"];
            if (IsolatedStorageSettings.ApplicationSettings.Contains("URL"))
                URL.Text = (string)IsolatedStorageSettings.ApplicationSettings["URL"];
            if (IsolatedStorageSettings.ApplicationSettings.Contains("Pwd"))
                Password.Password = (string)IsolatedStorageSettings.ApplicationSettings["Pwd"];
            if (IsolatedStorageSettings.ApplicationSettings.Contains("Save"))
                Save.IsChecked = (bool)IsolatedStorageSettings.ApplicationSettings["Save"];

            if (isLogged == true)
            {
                object sender = new object();
                System.Windows.Input.GestureEventArgs ert = new System.Windows.Input.GestureEventArgs();
                Button_Tap(sender, ert);
            }

            base.OnNavigatedTo(e);
        }
        // Exemple de code pour la conception d'une ApplicationBar localisée
        //private void BuildLocalizedApplicationBar()
        //{
        //    // Définit l'ApplicationBar de la page sur une nouvelle instance d'ApplicationBar.
        //    ApplicationBar = new ApplicationBar();

        //    // Crée un bouton et définit la valeur du texte sur la chaîne localisée issue d'AppResources.
        //    ApplicationBarIconButton appBarButton = new ApplicationBarIconButton(new Uri("/Assets/AppBar/appbar.add.rest.png", UriKind.Relative));
        //    appBarButton.Text = AppResources.AppBarButtonText;
        //    ApplicationBar.Buttons.Add(appBarButton);

        //    // Crée un nouvel élément de menu avec la chaîne localisée d'AppResources.
        //    ApplicationBarMenuItem appBarMenuItem = new ApplicationBarMenuItem(AppResources.AppBarMenuItemText);
        //    ApplicationBar.MenuItems.Add(appBarMenuItem);
        //}

        private void Button_Tap(object sender, System.Windows.Input.GestureEventArgs e)
        {
            if (!string.IsNullOrEmpty(Login.Text) && !string.IsNullOrEmpty(URL.Text) && !string.IsNullOrEmpty(Password.Password))
            {
                //tenter connection
                HttpWebRequest requete = (HttpWebRequest)HttpWebRequest.Create("http://localhost:8081/web/app_dev.php/service/1/op/list");
                requete.Method = "POST";
                requete.ContentType = "application/json";
       
                PhoneApplicationService.Current.State["URL"] = URL.Text;
                PhoneApplicationService.Current.State["login"] = Login.Text;
                PhoneApplicationService.Current.State["Pwd"] = Password.Password;
                PhoneApplicationService.Current.State["Save"] = Save.IsChecked.ToString();
                //le token
                
                if (Save.IsChecked == true)
                {
                    IsolatedStorageSettings.ApplicationSettings["logged"] = true;
                    IsolatedStorageSettings.ApplicationSettings["Login"] = Login.Text;
                    IsolatedStorageSettings.ApplicationSettings["URL"] = URL.Text;
                    IsolatedStorageSettings.ApplicationSettings["Pwd"] = Password.Password;
                    IsolatedStorageSettings.ApplicationSettings["Save"] = true;
                }
                else
                {
                    IsolatedStorageSettings.ApplicationSettings["Save"] = false; 
                }
                try
                {
                    NavigationService.Navigate(new Uri("/View/MvvmViewListe.xaml", UriKind.RelativeOrAbsolute));
                }
                catch (Exception exc)
                {
                    string toto = exc.Message;
                }
            }
            else
            {
                if (string.IsNullOrEmpty(Login.Text) && string.IsNullOrEmpty(URL.Text) && string.IsNullOrEmpty(Password.Password))
                {
                    Erreur.Text = "Veuillez remplir tous les champs.";
                }
                    // Gerer les codes de retours de l'API
                else
                {
                    Erreur.Text = "Mauvais mot de passe ou identifiants";
                }
                Erreur.Visibility = System.Windows.Visibility.Visible;
            }
        }

    }
}