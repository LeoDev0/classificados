<?php 
ob_start();
require 'templates/header.php';

$anuncios = new Anuncios($pdo);
// $totalAnuncios = $anuncios->getTotalAnuncios($filtros);

// $user = new Usuario($pdo);
// $totalUsuarios = $user->getTotalUsuarios();

// $categorias = new Categorias($pdo);
// $categorias = $categorias->getLista();

// Paginação dos anúncios

$page = 1;  # página padrão, obviamente é a 1

$limitePorPagina = 2;  # limite de carregamento de anúncios por página
$totalPaginas = ceil($totalAnuncios['total'] / $limitePorPagina);

if (isset($_GET['page']) && !empty($_GET['page'])) {
  $page = $_GET['page'];
}

$nextPage = $page + 1;
$previousPage = $page - 1;

$anuncios = $anuncios->getUltimosAnuncios($page, $limitePorPagina, $filtros);

// Se o usuário estiver logado, instanciar objeto da classe e pegar os dados cadastrais dele 
if (isset($_SESSION['user_id'])) {
  $user = $user->getDados($_SESSION['user_id']);
}
?>


<div class="container-fluid">

  <div style="margin-top:20px;" class="jumbotron">
    <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
    <h1 class="text-center" style="margin-bottom: 30px;">Olá <?= ucfirst($user['nome']) ?>, seja bem-vindo.</h1>
    <?php endif; ?>
    <h2>Nossa loja já possui <?= $totalAnuncios['total'] ?> anúncios e <a href="vendedores.php"><?= $totalUsuarios['total'] ?> usuários cadastrados.</a></h2><br>

    <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
    <a href="add_anuncio.php" class="btn btn-warning btn-lg">Quero Anunciar</a>
    <?php else: ?>
    <a class="btn btn-warning btn-lg" data-toggle="modal" data-target="#login-window">Quero Anunciar</a>
    <?php endif; ?>
  </div>

  <div class="row">

    <div class="col-sm-3">
      <h4>Pesquisa Avançada</h4>
      <form id="form-filtros" method="get">

        <div class="form-group">
          <label for="categoria">Categoria:</label>
          <select name="filtros[categoria]" class="form-control">
          <option></option>
          <?php foreach($categorias as $categoria): ?>
          <option value="<?= $categoria['id'] ?>" <?= ($categoria['id'] == $filtros['categoria']) ? 'selected' : '' ?> ><?= $categoria['nome'] ?></option>
          <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="preco">Preço:</label>
          <select name="filtros[preco]" class="form-control">
            <option></option>
            <option value="1-100" <?= ($filtros['preco'] == '1-100') ? 'selected' : ''?>>R$ 1 - 100</option>
            <option value="101-250" <?= ($filtros['preco'] == '101-250') ? 'selected' : ''?>>R$ 101 - 250</option>
            <option value="251-500" <?= ($filtros['preco'] == '251-500') ? 'selected' : ''?>>R$ 251 - 500</option>
            <option value="501-1000" <?= ($filtros['preco'] == '501-1000') ? 'selected' : ''?>>R$ 501 - 1000</option>
            <option value="1001-1000000" <?= ($filtros['preco'] == '1001-1000000') ? 'selected' : ''?>>Mais de R$ 1000</option>
          </select>
        </div>

        <div class="form-group">
          <label for="estado">Estado de conservação:</label>
          <select name="filtros[estado]" class="form-control">
            <option></option>
            <option value="0" <?= ($filtros['estado'] == '0') ? 'selected' : '' ?>>Ruim</option>
            <option value="1" <?= ($filtros['estado'] == '1') ? 'selected' : '' ?>>Bom</option>
            <option value="2" <?= ($filtros['estado'] == '2') ? 'selected' : '' ?>>Ótimo</option>
          </select>
        </div>

        <button class="btn btn-info">Buscar</button>
      </form>
    </div>

    <div class="col-sm-9">
      <h4>Últimos anúncios</h4>
      <table class="table table-striped">
        <tbody>
        <?php foreach ($anuncios as $anuncio): ?>
          <tr>
            <td>
              <?php if (empty($anuncio['url'])): ?>
              <img height="80" src="assets/images/anuncios/default.jpg" alt="foto-anuncio">
              <?php else: ?>
              <img height="100" src="assets/images/anuncios/<?= $anuncio['url'] ?>" alt="foto-anuncio">
              <?php endif; ?>
            </td>
            <td>
              <a href="produto.php?id=<?= $anuncio['id'] ?>"><?= $anuncio['titulo'] ?></a>
              <p><?= $anuncio['categoria'] ?></p>
            </td>
            <td>R$ <?= number_format($anuncio['valor'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div>

  <nav aria-label="Paginação">
    <ul class="pagination justify-content-center">

      <li class="page-item <?= ($previousPage == 0) ? 'disabled' : ''; ?>">
        <a class="page-link" href="index.php?page=<?= $previousPage ?>" aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
          <span class="sr-only">Previous</span>
        </a>
      </li>

      <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
      <li class="page-item <?= ($page == $i) ? 'active' : ''; ?>">
        <a class="page-link" href="index.php?<?php
          $w = $_GET;
          $w['page'] = $i;
          echo http_build_query($w);
          ?>"><?= $i ?>
        </a>
      </li>
      <? endfor; ?>

      <li class="page-item <?= ($nextPage > $totalPaginas) ? 'disabled' : ''; ?>">
        <a class="page-link" href="index.php?page=<?= $nextPage ?>" aria-label="Next">
          <span aria-hidden="true">&raquo;</span>
          <span class="sr-only">Next</span>
        </a>
      </li>

    </ul>
  </nav>

</div>

<?php
require 'templates/footer.php';
ob_end_flush();
?>