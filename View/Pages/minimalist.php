<style>
.payment-logo {
    display: flex;
    justify-content: center;
}
.bloc-cb {
	/*display: flex;
	flex-direction: column;
	flex-wrap: wrap;
	justify-content: center;
	align-items: center;*/
}
.bloc-cb img {
    height: 3.2rem;
    margin: 1.6rem 0;
}
.page-title {
    text-align: center;
    text-transform: capitalize;
    font-weight: 400;
    font-size: 4rem;
    font-family: Oswald;
    margin-bottom: 2.4rem;
}
.page-title::after {
    content: "";
    border-bottom: 0.2rem solid #ff0000;
    padding-top: .8rem;
    width: 16rem;
    margin: 0 auto;
    display: block;
}
</style>
<div class="page-title"><?= $page->title; ?></div>
<?= $page->content; ?>