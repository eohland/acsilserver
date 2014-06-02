using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Navigation;
using Microsoft.Phone.Controls;
using Microsoft.Phone.Shell;


namespace Acsilserver1.View
{
    public partial class MvvmViewDetails : PhoneApplicationPage
    {
        private int index;
        // Constructor
        public MvvmViewDetails()
        {
            InitializeComponent();
        }

        protected override void OnNavigatedTo(System.Windows.Navigation.NavigationEventArgs e)
        {

            if (DataContext == null)
            {
                string selectedIndex = "";
                if (NavigationContext.QueryString.TryGetValue("selectedItem", out selectedIndex))
                {
                    index = int.Parse(selectedIndex);
                    DataContext = App.ViewModel.Items[index];
                }
            }

            base.OnNavigatedTo(e);
        }

        private void AppBarEdit_Click(object sender, EventArgs e)
        {
            TextBoxName.IsReadOnly = false;
        }

        private void AppBarSave_Click(object sender, EventArgs e)
        {
            App.ViewModel.Items[index].Name = TextBoxName.Text;
        }


    }
}